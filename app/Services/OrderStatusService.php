<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Exception;

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

    public function changeStatus(Order $order, string $toStatus, ?string $reason = null, bool $force = false, int $adminId = 1 )
    {
        if (!$force && !$this->canTransition($order->status, $toStatus)) {
            throw new Exception("Invalid order transition from '{$order->status}' to '{$toStatus}'.");
        }

        $order->status = $toStatus;
        $order->status_changed_by = $adminId;
        $order->status_changed_at = Carbon::now();

        if ($toStatus === 'cancelled') {
            $order->cancel_reason = $reason;
            $order->cancelled_by = $adminId;
            $order->cancelled_at = Carbon::now();
        }

        $order->save();

        return $order;
    }

    public function canTransition(string $from, string $to): bool
    {
        if ($from === $to) return true;

        return isset($this->allowedTransitions[$from]) &&
            in_array($to, $this->allowedTransitions[$from]);
    }

    public function getAvailableTransitions(string $currentStatus): array
    {
        return $this->allowedTransitions[$currentStatus] ?? [];
    }
}
