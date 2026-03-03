<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Exception;

class OrderStatusService
{
    // protected $allowedTransitions = [
    //     'pending' => ['order_confirmation', 'cancelled'],
    //     'order_confirmation' => ['processing', 'cancelled'],
    //     'processing' => ['ready_to_ship', 'shipped', 'partially_shipped', 'dispatched', 'cancelled'],
    //     'ready_to_ship' => ['shipped', 'partially_shipped', 'dispatched', 'cancelled'],
    //     'partially_shipped' => ['shipped', 'dispatched', 'cancelled'],
    //     'dispatched' => ['shipped', 'out_for_delivery', 'delivered', 'returned'],
    //     'shipped' => ['out_for_delivery', 'delivered', 'returned'],
    //     'out_for_delivery' => ['delivered', 'returned', 'failed_delivery'],
    //     'delivered' => ['completed', 'returned'],
    //     'completed' => ['returned'],
    // ];

    protected $allowedTransitions = [
        'pending' => ['order_confirmation', 'cancelled'],
        'order_confirmation' => ['partially_dispatched', 'dispatched', 'cancelled'],
        'partially_dispatched' => ['dispatched', 'cancelled'],
        'dispatched' => ['ready_to_ship', 'cancelled'],
        'ready_to_ship' => ['shipped'],
        'shipped' => ['out_for_delivery', 'delivered'],
        'out_for_delivery' => ['delivered', 'failed_delivery'],
        'delivered' => ['completed', 'returned'],
    ];

    protected $mailTriggerService;

    public function __construct(MailTriggerService $mailTriggerService)
    {
        $this->mailTriggerService = $mailTriggerService;
    }

    public function changeStatus(Order $order, string $toStatus, ?string $reason = null, bool $force = false, ?int $adminId = null )
    {
        if (!$force && !$this->canTransition($order->status, $toStatus)) {
            throw new Exception("Invalid order transition from '{$order->status}' to '{$toStatus}'.");
        }

        $statusActorId = $this->resolveAdminId($adminId);

        $order->status = $toStatus;
        $order->status_changed_by = $statusActorId;
        $order->status_changed_at = Carbon::now();

        if ($toStatus === 'cancelled') {
            $order->cancel_reason = $reason;
            $order->cancelled_by = $statusActorId;
            $order->cancelled_at = Carbon::now();
        }

        $order->save();
        $this->mailTriggerService->sendOrderStatusUpdate($order, $toStatus);

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

    protected function resolveAdminId(?int $adminId): ?int
    {
        if (!$adminId) {
            return null;
        }

        return Admin::where('id', $adminId)->exists() ? $adminId : null;
    }
}
