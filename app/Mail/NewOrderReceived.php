<?php

namespace App\Mail;

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

    /**
     * Create a new message instance.
     */
    public function __construct($order, $orderUrl)
    {
        $this->order = $order;
        $this->user = $order->user;
        $this->orderUrl = $orderUrl;
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
                'order' => $this->order,
                'user' => $this->user,
                'orderUrl' => $this->orderUrl,
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
