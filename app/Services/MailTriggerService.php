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

        try {
            Mail::to($order->user->email)->queue(
                new OrderStatusMail($order->user, $order, $newStatus)
            );
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send {$newStatus} email for Order #{$order->order_number}: " . $e->getMessage());
            return false;
        }
    }
}
