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
        // Eager load items, product, and variant for admin order details
        return Order::with(['items.product', 'items.variant', 'user'])->find($id);
    }

    public function updateOrderStatus($id, $status)
    {
        $order = Order::find($id);
        if ($order) {
            $order->status = $status;
            $order->status_updated_by = auth()->guard('admin')->id();
            $order->status_updated_at = now();
            $order->save();
            return $order;
        }
        return null;
    }

    public function getDataForDataTable()
    {
        return Order::select(['id', 'order_number', 'user_id', 'total', 'status', 'payment_method', 'cancel_reason', 'created_at'])->orderBy('created_at', 'desc');
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

    public function cancelOrder($id, $cancelReason)
    {
        $order = Order::find($id);
        if ($order) {
            $order->status = 'cancelled';
            $order->cancel_reason = $cancelReason;
            $order->cancelled_by = auth()->guard('admin')->id();
            $order->cancelled_at = now();
            $order->save();
            return $order;
        }
        return null;
    }
}
