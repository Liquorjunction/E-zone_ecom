<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Mail\OrderStatusMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailTriggerService
{
    public function sendOrderStatusUpdate(Order $order, string $newStatus)
    {
        $mailableStatuses = ['order_confirmation', 'delivered', 'cancelled', 'order_placed', 'shipped', 'out_for_delivery'];

        if (!in_array(strtolower($newStatus), $mailableStatuses)) {
            return false;
        }

        $toEmail = $order->email ?? $order->user?->email;
        if (empty($toEmail)) {
            Log::warning("Cannot send {$newStatus} email for Order #{$order->order_number}: no recipient email found.");
            return false;
        }

        try {
            Mail::to($toEmail)->queue(
                new OrderStatusMail($order->user, $order, $newStatus)
            );
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send {$newStatus} email for Order #{$order->order_number}: " . $e->getMessage());
            return false;
        }
    }

    public function sendAdminNewOrderMail(Order $order)
    {
        try {
            $adminEmail = config('mail.admin_address', config('mail.from.address'));
            $orderUrl = route('admin.orders.show', $order->id);
            Mail::to($adminEmail)->queue(new \App\Mail\NewOrderReceived($order, $orderUrl));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to queue admin new order mail', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
            ]);
            return false;
        }
    }
}
