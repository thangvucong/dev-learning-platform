<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CheckoutLoginOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Plain 6-digit OTP (shown only in email body).
     *
     * @var string
     */
    public $otp;

    /**
     * Create a new message instance.
     *
     * @param  string  $otp
     */
    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('Your sign-in code'))
            ->view('emails.checkout-login-otp');
    }
}
