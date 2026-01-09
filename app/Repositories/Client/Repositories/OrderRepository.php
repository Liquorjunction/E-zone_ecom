<?php

namespace App\Repositories\Client\Repositories;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Client\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderRepository implements OrderRepositoryInterface
{
    public function createOrderFromCart(Cart $cart, array $data)
    {
        $order = null;

        DB::transaction(function() use ($cart, $data, &$order) {
            // Calculate cart totals and discounts
            $cartTotals = $cart->calculateTotals();
            $subtotal = $cartTotals['subtotal'] ?? 0;
            $totalItemDiscount = $cartTotals['total_discount'] ?? 0;
            $subtotalAfterItemDiscount = $cartTotals['total'] ?? 0;

            // Coupon data
            $couponDiscount = $cart->coupon_discount_amount ?? 0;
            $totalAfterCoupon = max(0, $subtotalAfterItemDiscount - $couponDiscount);

            $taxRate = config('tax.rate', 20.0);
            $taxAmount = round($totalAfterCoupon * ($taxRate / (100 + $taxRate)), 2);
            $taxableAmount = round($totalAfterCoupon - $taxAmount, 2);

            // Shipping data
            $shippingMethod = $data['shipping_method'] ?? 'standard';
            $shippingCost = $shippingMethod === 'express' ? 12.99 : 0.00;

            // Final total
            $finalTotal = round($totalAfterCoupon + $shippingCost, 2);

            $order = Order::create([
                'user_id' => $cart->user_id ?? null,
                'subtotal' => round($subtotal, 2),
                'discount_amount' => round($totalItemDiscount, 2),
                'coupon_id' => $cart->coupon_id ?? null,
                'coupon_code' => $cart->coupon_code ?? null,
                'coupon_discount_amount' => round($couponDiscount, 2),
                'shipping_method' => $shippingMethod,
                'shipping_cost' => round($shippingCost, 2),
                'tax_amount' => round($taxAmount, 2),
                'tax_percentage' => $taxRate,
                'taxable_amount' => $taxableAmount,
                'total' => $finalTotal,
                'status' => 'pending',
                'shipping_address' => $data['shipping_address'] ?? null,
                'phone_no' => $data['phone_no'] ?? null,
                'email' => $data['email'] ?? Auth::user()?->email ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'order_number' => 'EZ-ORD-'.uniqid(),
            ]);

            $total = 0;
            foreach ($cart->items as $item) {
                $variant = $item->variant ?: ProductVariant::withTrashed()->find($item->variant_id);
                $product = $item->product;

                $unitPriceOriginal = $item->unit_price ?? 0;
                $discountPerUnit = $item->discount_amount ?? 0;
                $unitPriceFinal = $item->final_price ?? 0;

                $lineSubtotal = round($unitPriceOriginal * $item->quantity, 2);
                $lineDiscount = round($discountPerUnit * $item->quantity, 2);
                $lineFinal = round($unitPriceFinal * $item->quantity, 2);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPriceFinal,
                    'unit_price_original' => round($unitPriceOriginal, 2),
                    'discount_amount' => round($discountPerUnit, 2),
                    'line_subtotal' => $lineSubtotal,
                    'line_discount' => $lineDiscount,
                    'total_price' => $lineFinal,
                    'line_total' => $lineFinal,
                ]);
                $total += $lineFinal;

                $remaining = $item->quantity;

                $inventories = Inventory::where('product_variant_id', $item->variant_id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                if ($inventories->isEmpty()) {
                    $inventories = Inventory::where('product_id', $item->product_id)
                        ->orderBy('created_at', 'asc')
                        ->get();
                }

                foreach ($inventories as $inv) {
                    if ($remaining <= 0) break;

                    $available = (float) $inv->quantity;
                    if ($available <= 0) continue;

                    $deduct = min($available, $remaining);
                    $before = $available;
                    $after = $before - $deduct;

                    $inv->quantity = $after;
                    $inv->save();

                    try {
                        InventoryTransaction::create([
                            'inventory_id' => $inv->id,
                            'type' => 'sale',
                            'quantity' => $deduct,
                            'rate' => $item->unit_price ?? 0,
                            'batch_no' => $inv->batch_no,
                            'created_by' => Auth::id() ?? ($order->user_id ?? null),
                            'description' => 'Order #' . $order->id,
                            'reference_type' => 'order',
                            'reference_id' => $order->id,
                            'before_quantity' => $before,
                            'after_quantity' => $after,
                            'quantity_change' => -1 * $deduct,
                        ]);
                    } catch (\Throwable $e) {
                        Log::error($e->getMessage());
                    }

                    $remaining -= $deduct;
                }

                if ($remaining > 0) {
                    $placeholder = Inventory::firstOrCreate([
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->variant_id,
                        'location_type' => 'warehouse',
                    ], [
                        'warehouse_id' => null,
                        'store_id' => null,
                        'quantity' => 0,
                        'cost_price' => $item->unit_price ?? 0,
                        'status' => 'active',
                    ]);

                    $before = (float) $placeholder->quantity;
                    $placeholder->quantity = $before - $remaining;
                    $placeholder->save();

                    try {
                        InventoryTransaction::create([
                            'inventory_id' => $placeholder->id,
                            'type' => 'sale',
                            'quantity' => $remaining,
                            'rate' => $item->unit_price ?? 0,
                            'batch_no' => $placeholder->batch_no,
                            'created_by' => Auth::id() ?? ($order->user_id ?? null),
                            'description' => 'Order #' . $order->id . ' (backorder)',
                            'reference_type' => 'order',
                            'reference_id' => $order->id,
                            'before_quantity' => $before,
                            'after_quantity' => $placeholder->quantity,
                            'quantity_change' => -1 * $remaining,
                        ]);
                    } catch (\Throwable $e) {
                        Log::error($e->getMessage());
                    }
                }
            }

            $order->total = $total;
            $order->order_number = 'EZ-ORD-'.str_pad($order->id, 4, '0', STR_PAD_LEFT);
            $order->save();

            $cart->items()->delete();
            $cart->delete();
        });

        return $order;
    }

    public function cancelOrder(int $orderId, ?string $reason = null)
    {
        $order = Order::with('items')->find($orderId);
        if (! $order) return false;

        if (in_array($order->status, ['cancelled', 'refunded'])) {
            return false;
        }

        DB::transaction(function() use ($order, $reason) {
            $txns = InventoryTransaction::where('reference_type', 'order')
                ->where('reference_id', $order->id)
                ->where('type', 'sale')
                ->get()
                ->groupBy('inventory_id');

            foreach ($txns as $inventoryId => $group) {
                $sum = $group->sum('quantity');
                $inv = Inventory::find($inventoryId);
                if (! $inv) continue;

                $before = $inv->quantity;
                $inv->quantity = $inv->quantity + $sum;
                $inv->save();

                try {
                    InventoryTransaction::create([
                        'inventory_id' => $inv->id,
                        'type' => 'return',
                        'quantity' => $sum,
                        'rate' => $inv->cost_price ?? 0,
                        'batch_no' => $inv->batch_no,
                        'created_by' => Auth::id() ?? ($order->user_id ?? null),
                        'description' => 'Order cancellation: '.$order->order_number,
                        'reference_type' => 'order_cancellation',
                        'reference_id' => $order->id,
                        'before_quantity' => $before,
                        'after_quantity' => $inv->quantity,
                        'quantity_change' => $sum,
                    ]);
                } catch (\Throwable $e) {
                    Log::error($e->getMessage());
                }
            }
            $order->status = 'cancelled';
            $order->save();
        });

        return true;
    }
}
