<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GuestOtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $code;
    public ?string $email;

    public function __construct(string $code, ?string $email = null)
    {
        $this->code  = $code;
        $this->email = $email;
    }

    public function build()
    {
        $subject = statictranslations('Your Verification Code') . ' - ' . config('app.name');

        return $this->subject($subject)
            ->view('client.emails.guest_otp')
            ->with([
                'code'  => $this->code,
                'email' => $this->email,
            ]);
    }
}
