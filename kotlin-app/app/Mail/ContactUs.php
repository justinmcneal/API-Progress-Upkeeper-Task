<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactUs extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $username;
    public $email;
    public $messageContent;

    public function __construct($username, $email, $messageContent)
    {
        $this->username = $username;
        $this->email = $email;
        $this->messageContent = $messageContent;
    }

    public function build()
    {
        return $this->view('emails.contact-form')
                    ->subject('Contact Form Submission')
                    ->with([
                        'username' => $this->username,
                        'email' => $this->email,
                        'messageContent' => $this->messageContent,
                    ]);
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Contact Us',
            from: new Address('lumpiajavarice@gmail.com', 'HTML-JAVA TEAM'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact',
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
