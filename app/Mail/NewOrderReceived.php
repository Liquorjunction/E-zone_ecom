<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $user;
    public $orderUrl;
    public $customerName;
    public $customerEmail;
    public $customerPhone;
    public $isGuest;

    /**
     * Create a new message instance.
     */
    public function __construct($order, $orderUrl)
    {
        $this->afterCommit();

        $this->order = $order;
        $this->user = $order->user ?? null;
        $this->orderUrl = $orderUrl;

        $shipping = $order->shipping_address ?? [];
        $this->isGuest = method_exists($order, 'isGuest')
            ? $order->isGuest()
            : (($order->client_type ?? '') === 'guest' || is_null($order->user_id));

        $this->customerName = $this->user?->name
            ?? $order->guestUser?->name
            ?? $order->shippingAddressRecord?->name
            ?? ($shipping['name'] ?? ($this->isGuest ? 'Client Invité' : 'Client'));

        $this->customerEmail = $order->email
            ?? $this->user?->email
            ?? $order->guestUser?->email
            ?? ($shipping['email'] ?? '—');

        $this->customerPhone = $order->phone_no
            ?? $this->user?->phone
            ?? $order->guestUser?->phone
            ?? ($shipping['phone'] ?? '');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle Commande Reçue - #' . ($this->order->order_number ?? ''),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'admin.emails.new_orderReceived',
            with: [
                'order'         => $this->order,
                'user'          => $this->user,
                'orderUrl'      => $this->orderUrl,
                'customerName'  => $this->customerName,
                'customerEmail' => $this->customerEmail,
                'customerPhone' => $this->customerPhone,
                'isGuest'       => $this->isGuest,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
