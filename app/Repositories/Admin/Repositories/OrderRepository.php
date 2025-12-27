<?php

namespace App\Repositories\Admin\Repositories;
use App\Models\Order;
use App\Repositories\Admin\Interfaces\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function getAllOrders()
    {
        return Order::all();
    }

    public function getOrderById($id)
    {
        return Order::find($id);
    }

    public function updateOrderStatus($id, $status)
    {
        $order = Order::find($id);
        if ($order) {
            $order->status = $status;
            $order->save();
            return $order;
        }
        return null;
    }

    public function getDataForDataTable()
    {
        return Order::select(['id', 'order_number', 'user_id', 'total', 'status', 'payment_method', 'created_at']);
    }

    public function getCustomerNameByOrderId($orderId)
    {
        $order = Order::with('user')->find($orderId);
        if (! $order) {
            return null;
        }

        if ($order->user) {
            return $order->user->name ?? ($order->user->email ?? 'N/A');
        }

        // Fallback: if customer name is stored on order record under a different field
        return $order->customer_name ?? $order->shipping_address['name'] ?? null;
    }
}
