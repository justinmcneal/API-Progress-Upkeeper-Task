<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        // Validate the request input (only the message is needed from the form)
        $validatedData = $request->validate([
            'message' => 'required|string',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Prepare the email data
        $emailData = [
            'username' => $user->username,  // Fetch username from the authenticated user
            'email' => $user->email,        // Fetch email from the authenticated user
            'message' => $validatedData['message'],
        ];

        // Send the email
        Mail::send('emails.contact', $emailData, function ($message) use ($user) {
            $message->to('your_email@example.com') // Replace with your support email
                ->subject('New Contact Form Submission from ' . $user->username);
        });

        return response()->json(['message' => 'Message sent successfully!']);
    }
}
