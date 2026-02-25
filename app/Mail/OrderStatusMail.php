<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $order;
    public $status;
    public $reviewUrl;
    public $trackingUrl;

    public function __construct(User $user, Order $order, $status)
    {
        $this->user = $user;
        $this->order = $order;
        $this->status = strtolower($status);
        $this->reviewUrl = route('order-detail', $order->order_number);
        // Provide tracking URL for shipped and out_for_delivery mails
        if (in_array($this->status, ['shipped', 'out_for_delivery'])) {
            $this->trackingUrl = $order->tracking_url ?? null;
        } else {
            $this->trackingUrl = null;
        }
    }

    public function build()
    {
        $templateMap = [
            'order_confirmation' => 'client.emails.order_confirmed',
            'dispatched' => 'client.emails.order_dispatched',
            'delivered'  => 'client.emails.order_delivered',
            'cancelled'  => 'client.emails.order_cancelled',
            'order_placed' => 'client.emails.order_placed',
            'shipped' => 'client.emails.order_shipped',
            'out_for_delivery' => 'client.emails.order_outForDelivery',
        ];

        $view = $templateMap[$this->status] ?? 'client.emails.order_confirmed';

        return $this->subject('Order ' . ucfirst(str_replace('_', ' ', $this->status)) . ' - #' . $this->order->order_number)
                ->view($view)
                ->with(['trackingUrl' => $this->trackingUrl]);
    }
}
