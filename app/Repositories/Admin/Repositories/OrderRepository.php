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

    public function getStatusList(OrderStatusService $orderStatusService)
    {
        return $orderStatusService->getStatuses();
    }

    public function getPaymentMethodsList()
    {
        return Order::first()?->PaymentMethods() ?? [];
    }

    public function getDataForDataTable($request)
    {
        $query = Order::with(['cancelledBy:id,name'])
            ->select(['id', 'order_number', 'user_id', 'email', 'total', 'status', 'payment_method', 'transaction_id', 'cancel_reason', 'cancelled_by', 'created_at'])
            ->orderBy('created_at', 'desc');

        $getInput = function (string $key) use ($request) {
            if (is_array($request)) {
                return $request[$key] ?? null;
            }

            if (is_object($request) && method_exists($request, 'input')) {
                return $request->input($key);
            }

            return null;
        };

        $status = $getInput('status');
        if (!empty($status)) {
            $query->where('status', $status);
        }

        $paymentMethod = $getInput('payment_method');
        if (!empty($paymentMethod)) {
            $query->where('payment_method', $paymentMethod);
        }

        $startDate = $getInput('start_date');
        $endDate = $getInput('end_date');
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif (!empty($startDate)) {
            $query->where('created_at', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $query->where('created_at', '<=', $endDate);
        }

        $paymentStatus = $getInput('payment_status');
        if ($paymentStatus === 'paid') {
            $query->where(function ($q) {
                $q->whereNotNull('transaction_id')
                    ->orWhere(function ($sub) {
                        $sub->where('payment_method', 'cod')
                            ->whereIn('status', ['delivered', 'completed']);
                    });
            });
        } elseif ($paymentStatus === 'cancelled') {
            $query->where('status', 'cancelled');
        } elseif ($paymentStatus === 'pending') {
            $query->whereNull('transaction_id')
                ->where('status', '!=', 'cancelled')
                ->where(function ($q) {
                    $q->where('payment_method', '!=', 'cod')
                        ->orWhereNotIn('status', ['delivered', 'completed']);
                });
        }

        return $query;
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
