<?php

namespace App\Services\Admin;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class OrderStatusService
{
	protected $allowedTransitions = [
		'pending' => ['order_confirmation'],
		'order_confirmation' => ['processing', 'cancelled'],
		'processing' => ['ready_to_ship', 'cancelled'],
		'ready_to_ship' => ['shipped', 'cancelled'],
		'shipped' => ['out_for_delivery'],
		'out_for_delivery' => ['delivered'],
		'delivered' => ['completed'],
	];

	public function changeStatus(Order $order, string $toStatus, ?string $reason = null)
	{
		$fromStatus = $order->status;
		if (!$this->canTransition($fromStatus, $toStatus)) {
			return null;
		}

		$order->status = $toStatus;
		$order->status_changed_by = Auth::guard('admin')->id();
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
		if (!isset($this->allowedTransitions[$from])) {
			return false;
		}
		return in_array($to, $this->allowedTransitions[$from], true);
	}

	public function getAllowedNextStatuses(string $from): array
	{
		return $this->allowedTransitions[$from] ?? [];
	}
}
