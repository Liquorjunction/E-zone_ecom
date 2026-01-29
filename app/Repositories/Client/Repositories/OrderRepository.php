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
use App\Repositories\Client\Interfaces\CartRepositoryInterface as CartRepo;
use App\Services\PriceCalculatorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderRepository implements OrderRepositoryInterface
{
    public function getOrdersWithRelations(array $filters, array $relations = [], int $perPage = 10, array $extra = [])
    {
        $query = Order::query();
        foreach ($filters as $key => $value) {
            $query->where($key, $value);
        }

        // Search by order number, product name, etc.
        if (!empty($extra['search'])) {
            $search = $extra['search'];
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%$search%")
                  ->orWhereHas('items.product', function($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%") ;
                  });
            });
        }

        // Date range filter
        if (!empty($extra['date_from'])) {
            $query->whereDate('created_at', '>=', $extra['date_from']);
        }
        if (!empty($extra['date_to'])) {
            $query->whereDate('created_at', '<=', $extra['date_to']);
        }

        if (!empty($relations)) {
            $query->with($relations);
        }

        // Sorting
        switch ($extra['sort'] ?? 'latest') {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'amount_high':
                $query->orderBy('total', 'desc');
                break;
            case 'amount_low':
                $query->orderBy('total', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    public function findOrderWithRelations(int $orderId, array $relations = [])
    {
        $query = Order::query();
        if (!empty($relations)) {
            $query->with($relations);
        }
        return $query->find($orderId);
    }

    public function getLatestOrderForUser(int $userId, array $relations = [])
    {
        $query = Order::where('user_id', $userId);
        if (!empty($relations)) {
            $query->with($relations);
        }
        return $query->latest()->first();
    }

    public function findOrder(int $orderId)
    {
        return Order::find($orderId);
    }

    public function createOrderFromCart(Cart $cart, array $data, CartRepo $cartRepo, PriceCalculatorService $priceService)
    {
        $order = null;

        DB::transaction(function () use ($cart, $data, &$order, $priceService) {
            $shippingMethod = $data['shipping_method'] ?? 'standard';
            $pricing = $priceService->calculate($cart, $shippingMethod);

            $order = Order::create([
                'user_id' => $cart->user_id ?? null,
                'subtotal' => $pricing['subtotal'],
                'discount_amount' => $pricing['total_item_discount'],
                'coupon_id' => $cart->coupon_id ?? null,
                'coupon_code' => $cart->coupon_code ?? null,
                'coupon_discount_amount' => $pricing['coupon_discount'],
                'shipping_method' => $shippingMethod,
                'shipping_cost' => $pricing['shipping_cost'],
                'tax_amount' => $pricing['tax_amount'],
                'tax_percentage' => $pricing['tax_rate'],
                'taxable_amount' => $pricing['taxable_amount'],
                'total' => $pricing['final_total'],
                'status' => 'pending',
                'shipping_address' => $data['shipping_address'] ?? null,
                'billing_address' => $data['billing_address'] ?? ($data['shipping_address'] ?? null),
                'phone_no' => $data['phone_no'] ?? null,
                'email' => $data['email'] ?? Auth::user()?->email ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'order_number' => 'EZMA/ORD/' . uniqid(),
            ]);

            $orderItems = [];
            $inventoryTxns = [];

            foreach ($cart->items as $item) {
                $variant = $item->variant ?: ProductVariant::withTrashed()->find($item->variant_id);

                $unitPriceOriginal = $item->unit_price ?? 0;
                $discountPerUnit = $item->discount_amount ?? 0;
                $unitPriceFinal = $item->final_price ?? 0;

                $lineSubtotal = round($unitPriceOriginal * $item->quantity, 2);
                $lineDiscount = round($discountPerUnit * $item->quantity, 2);
                $lineFinal = round($unitPriceFinal * $item->quantity, 2);

                $orderItems[] = [
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
                ];

                // Inventory Logic with Locking
                $remaining = $item->quantity;

                // Lock rows for update to prevent race conditions
                $inventories = Inventory::where('product_variant_id', $item->variant_id)
                    ->orderBy('created_at', 'asc')
                    ->lockForUpdate() // Added locking
                    ->get();

                if ($inventories->isEmpty()) {
                    $inventories = Inventory::where('product_id', $item->product_id)
                        ->orderBy('created_at', 'asc')
                        ->lockForUpdate() // Added locking
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
                    $inv->reserved_quantity = ($inv->reserved_quantity ?? 0) + $deduct;
                    $inv->save();

                    $inventoryTxns[] = [
                        'inventory_id' => $inv->id,
                        'type' => 'sale',
                        'quantity' => $deduct,
                        'rate' => $item->unit_price ?? 0,
                        'batch_no' => $inv->batch_no,
                        'created_by' => Auth::id() ?? ($order->user_id ?? null),
                        'description_type' => 'normal',
                        'reference_type' => 'order',
                        'reference_id' => $order->id,
                        'before_quantity' => $before,
                        'after_quantity' => $after,
                        'quantity_change' => -1 * $deduct,
                    ];

                    $remaining -= $deduct;
                }

                // Backorder handling
                if ($remaining > 0) {
                    $placeholder = Inventory::firstOrCreate([
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->variant_id,
                        'location_type' => 'warehouse',
                    ], [
                        'warehouse_id' => null,
                        'store_id' => null,
                        'quantity' => 0,
                        'reserved_quantity' => 0,
                        'cost_price' => $item->unit_price ?? 0,
                        'status' => 'active',
                    ]);

                    // We re-fetch to lock this specific row if it existed or was just created
                    $placeholder = Inventory::where('id', $placeholder->id)->lockForUpdate()->first();

                    $before = (float) $placeholder->quantity;
                    $placeholder->quantity = $before - $remaining;
                    $placeholder->reserved_quantity = ($placeholder->reserved_quantity ?? 0) + $remaining;
                    $placeholder->save();

                    $inventoryTxns[] = [
                        'inventory_id' => $placeholder->id,
                        'type' => 'sale',
                        'quantity' => $remaining,
                        'rate' => $item->unit_price ?? 0,
                        'batch_no' => $placeholder->batch_no,
                        'created_by' => Auth::id() ?? ($order->user_id ?? null),
                        'description_type' => 'backorder',
                        'reference_type' => 'order',
                        'reference_id' => $order->id,
                        'before_quantity' => $before,
                        'after_quantity' => $placeholder->quantity,
                        'quantity_change' => -1 * $remaining,
                    ];
                }
            }

            foreach ($orderItems as $oi) {
                OrderItem::create($oi);
            }

            // Update order number only
            $order->order_number = 'EZMA/ORD/' . str_pad($order->id, 4, '0', STR_PAD_LEFT);
            $order->save();

            foreach ($inventoryTxns as $txn) {
                try {
                    $desc = $txn['description_type'] === 'backorder'
                        ? 'Order #' . $order->order_number . ' (backorder)'
                        : 'Order #' . $order->order_number;
                    $txnData = $txn;
                    unset($txnData['description_type']);
                    $txnData['description'] = $desc;
                    InventoryTransaction::create($txnData);
                } catch (\Throwable $e) {
                    Log::error($e->getMessage());
                }
            }

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

        DB::transaction(function () use ($order, $reason) {
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
                $beforeReserved = $inv->reserved_quantity ?? 0;
                $inv->quantity = $inv->quantity + $sum;
                $inv->reserved_quantity = max(0, $beforeReserved - $sum);
                $inv->save();

                try {
                    InventoryTransaction::create([
                        'inventory_id' => $inv->id,
                        'type' => 'return',
                        'quantity' => $sum,
                        'rate' => $inv->cost_price ?? 0,
                        'batch_no' => $inv->batch_no,
                        'created_by' => Auth::id() ?? ($order->user_id ?? null),
                        'description' => 'Order cancellation: ' . $order->order_number,
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
