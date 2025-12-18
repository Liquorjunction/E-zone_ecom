<?php

namespace App\Repositories\Client\Repositories;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\Client\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    public function createOrderFromCart(Cart $cart, array $data)
    {
        $order = null;
        DB::transaction(function() use ($cart, $data, &$order) {
            // Create order first to get its real auto-increment ID, then build a stable order_number
            $order = Order::create([
                'user_id' => $cart->user_id ?? null,
                'total' => 0,
                'status' => 'pending',
                'shipping_address' => $data['shipping_address'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
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
}
