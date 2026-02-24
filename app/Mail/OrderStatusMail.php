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

    public function __construct(User $user, Order $order, $status)
    {
        $this->user = $user;
        $this->order = $order;
        $this->status = strtolower($status);
        $this->reviewUrl = route('order-detail', $order->order_number);
    }

    public function build()
    {
        $templateMap = [
            'confirmed'  => 'client.emails.order_confirmed',
            'dispatched' => 'client.emails.order_dispatched',
            'delivered'  => 'client.emails.order_delivered',
            'cancelled'  => 'client.emails.order_cancelled',
        ];

        $view = $templateMap[$this->status] ?? 'client.emails.order_confirmed';

        return $this->subject('Order ' . ucfirst($this->status) . ' - #' . $this->order->order_number)
                    ->view($view);
    }
}
