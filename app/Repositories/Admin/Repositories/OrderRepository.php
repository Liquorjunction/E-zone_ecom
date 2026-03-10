<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\Admin\Interfaces\OrderRepositoryInterface;

use App\Services\OrderStatusService;

class OrderRepository implements OrderRepositoryInterface
{
    public function getAllOrders()
    {
        return Order::all();
    }

    public function getOrderById($id)
    {
        return Order::with(['items.product', 'items.variant', 'user'])->find($id);
    }

    public function getOrderItemById($id)
    {
        return OrderItem::with('product', 'variant')->find($id);
    }

    public function getDataForDataTable()
    {
        return Order::with(['cancelledBy:id,name'])
            ->select(['id', 'order_number', 'user_id', 'email', 'total', 'status', 'payment_method', 'transaction_id', 'cancel_reason', 'cancelled_by', 'created_at'])
            ->orderBy('created_at', 'desc');
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

        return $order->customer_name ?? $order->shipping_address['name'] ?? null;
    }

    public function findOrderWithRelations(int $orderId, array $relations = [])
    {
        $query = Order::query();
        if (!empty($relations)) {
            $query->with($relations);
        }
        return $query->find($orderId);
    }

    public function getOrderWithItemsAndInventory($orderId)
    {
        return Order::with(['orderItems.product', 'orderItems.variant.inventories'])->find($orderId);
    }
}
