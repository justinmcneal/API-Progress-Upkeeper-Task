<?php

namespace App\Jobs;

use App\Mail\ContactUs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ContactUsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle(): void
    {
        \Log::info('Sending email to: ' . $this->data['email']);
        $mailable = new ContactUs($this->data);
        
        // Attempt to send the email
        try {
            Mail::to('lumpiajavarice@gmail.com')->send($mailable);
            \Log::info('Email sent successfully to: ' . $this->data['email']);
        } catch (\Exception $e) {
            \Log::error('Failed to send email: ' . $e->getMessage());
        }
    }
}

