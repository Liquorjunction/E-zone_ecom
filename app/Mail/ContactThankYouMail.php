<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactThankYouMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $ticket_number;
    public $subject_label;
    public $user_message;
    public $order_number;
    public $product_sku;

    public function __construct($name, $ticket_number, $subject_label, $user_message, $order_number = null, $product_sku = null)
    {
        $this->name = $name;
        $this->ticket_number = $ticket_number;
        $this->subject_label = $subject_label;
        $this->user_message = $user_message;
        $this->order_number = $order_number;
        $this->product_sku = $product_sku;
    }

    public function build()
    {
        return $this->subject('Merci de nous avoir contactés - Support ' . config('app.name'))
            ->view('client.emails.contact_thank_you')
            ->with([
            'name' => $this->name,
            'ticket_number' => $this->ticket_number,
            'subject_label' => $this->subject_label,
            'user_message' => $this->user_message,
            'order_number' => $this->order_number,
            'product_sku' => $this->product_sku,
            ]);
        }
}
