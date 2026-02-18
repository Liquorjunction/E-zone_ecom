<?php

namespace App\Services\Admin;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class OrderStatusService
{
    protected $allowedTransitions = [
        'pending' => ['order_confirmation', 'cancelled'],
        'order_confirmation' => ['processing', 'cancelled'],
        'processing' => ['ready_to_ship', 'shipped', 'partially_shipped', 'cancelled'],
        'ready_to_ship' => ['shipped', 'partially_shipped', 'cancelled'],
        'partially_shipped' => ['shipped', 'cancelled'],
        'shipped' => ['out_for_delivery', 'delivered', 'returned'],
        'out_for_delivery' => ['delivered', 'returned', 'failed_delivery'],
        'delivered' => ['completed', 'returned'],
        'completed' => ['returned'],
    ];

    public function changeStatus(Order $order, string $toStatus, ?string $reason = null)
    {
        // 1. Force transition if it's a system automated dispatch (bypassing strict user flow if needed)
        // Or strictly adhere to graph:
        if (!$this->canTransition($order->status, $toStatus)) {
            // Log error or throw exception in production
            return null;
        }

        $order->status = $toStatus;
        $order->status_changed_by = Auth::guard('admin')->id() ?? 1; // Fallback to 1 if system
        $order->status_changed_at = Carbon::now();

        if ($toStatus === 'cancelled') {
            $order->cancel_reason = $reason;
            $order->cancelled_by = Auth::guard('admin')->id();
            $order->cancelled_at = Carbon::now();
        }

        $order->save();
        return $order;
    }

    public function canTransition(string $from, string $to): bool
    {
        // Allow staying in same status (idempotency)
        if ($from === $to) return true;

        return isset($this->allowedTransitions[$from]) &&
            in_array($to, $this->allowedTransitions[$from]);
    }
}
