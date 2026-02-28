<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewContactUsEnquiryReceived extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The data for the contact enquiry email.
     */
    public $ticket_number;
    public $subject_label;
    public $name;
    public $email;
    public $phone;
    public $user_message;
    public $ticketUrl;
    public $product_sku;
    public $order_number;

    /**
     * Create a new message instance.
     */
    public function __construct($ticket_number, $subject_label, $name, $email, $phone, $user_message, $ticketUrl, $subject = null, $product_sku = null, $order_number = null)
    {
        $this->ticket_number = $ticket_number;
        $this->subject_label = $subject_label;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->user_message = $user_message;
        $this->ticketUrl = $ticketUrl;

        // product_sku for all except 'other' and 'complaint'
        if (!in_array($subject, ['other', 'complaint'])) {
            $this->product_sku = $product_sku;
        } else {
            $this->product_sku = null;
        }
        // order_number for all except 'other', 'complaint', 'bulk_purchase'
        if (!in_array($subject, ['other', 'complaint', 'bulk_purchase'])) {
            $this->order_number = $order_number;
        } else {
            $this->order_number = null;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Enquiry Received' . ($this->ticket_number ? " - {$this->ticket_number}" : ''),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'admin.emails.new_enquiry',
            with: [
                'ticket_number' => $this->ticket_number,
                'subject_label' => $this->subject_label,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'user_message' => $this->user_message,
                'ticketUrl' => $this->ticketUrl,
                'product_sku' => $this->product_sku ?? null,
                'order_number' => $this->order_number ?? null,
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
