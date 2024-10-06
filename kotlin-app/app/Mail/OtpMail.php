<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp; // Variable to hold OTP value

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($otp)
    {
        $this->otp = $otp; // Assign OTP value
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your OTP Code')
                    ->view('emails.otp') // This is the correct method (view not views)
                    ->with('otp', $this->otp); // Pass OTP to the view
    }
}
