<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactUs extends Mailable
{
    use Queueable, SerializesModels;

    public $username;
    public $email;
    public $message;

    public function __construct($username, $email, $message)
    {
        $this->username = $username;
        $this->email = $email;
        $this->message = $message;
    }

    public function build()
    {
        return $this->view('emails.contact')
                    ->with([
                        'username' => $this->username,
                        'email' => $this->email,
                        'message' => $this->message,
                    ]);
    }
}
