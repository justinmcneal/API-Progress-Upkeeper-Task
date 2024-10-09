<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class ContactUs extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $username;
    public $email;

    public function __construct($message)
    {
        $this->message = $message;
        $this->username = $username;
        $this->email = $email;

        // Fetch the authenticated user
        $user = Auth::user();
        $this->username = $user ? $user->username : 'Guest'; // Fallback if user is not authenticated
        $this->email = $user ? $user->email : 'No Email'; // Fallback if user is not authenticated
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
