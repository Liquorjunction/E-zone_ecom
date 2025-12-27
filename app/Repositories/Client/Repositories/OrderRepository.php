<?php

namespace App\Repositories\Client\Repositories;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Client\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    public function createOrderFromCart(Cart $cart, array $data)
    {
        $order = null;
        DB::transaction(function() use ($cart, $data, &$order) {
            // Create order first to get its real auto-increment ID, then build a stable order_number
            // Provide a temporary unique order_number so the DB insert doesn't fail
            $order = Order::create([
                'user_id' => $cart->user_id ?? null,
                'total' => 0,
                'status' => 'pending',
                'shipping_address' => $data['shipping_address'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'phone_no' => $data['phone_no'] ?? null,
                'order_number' => 'EZ-ORD-'.uniqid(),
            ]);

            $total = 0;
            foreach ($cart->items as $item) {
                $lineTotal = ($item->unit_price ?? 0) * $item->quantity;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $lineTotal,
                ]);
                $total += $lineTotal;

                // Deduct stock and record inventory transactions
                $remaining = $item->quantity;

                // Prefer variant-level inventory rows (FIFO)
                $inventories = Inventory::where('product_variant_id', $item->variant_id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                // Fallback to product-level rows if no variant rows found
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
                        // don't fail order creation for transaction logging issues
                    }

                    $remaining -= $deduct;
                }

                // If still remaining, create/adjust a placeholder inventory row (allow negative stock)
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
                        // ignore
                    }
                }
            }

            // Use the created order's ID to build a unique order number (avoids race conditions)
            $order->total = $total;
            $order->order_number = 'EZ-ORD-'.str_pad($order->id, 4, '0', STR_PAD_LEFT);
            $order->save();

            // clear cart
            $cart->items()->delete();
            $cart->delete();
        });

        return $order;
    }

    /**
     * Cancel an order, restore inventory and record transactions.
     */
    public function cancelOrder(int $orderId, ?string $reason = null)
    {
        $order = Order::with('items')->find($orderId);
        if (! $order) return false;

        if (in_array($order->status, ['cancelled', 'refunded'])) {
            return false; // already cancelled/refunded
        }

        DB::transaction(function() use ($order, $reason) {
            // Find related sale transactions for this order
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
                    // ignore logging failures
                }
            }

            // Mark order as cancelled
            $order->status = 'cancelled';
            $order->save();
        });

        return true;
    }
}
