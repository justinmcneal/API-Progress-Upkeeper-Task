<?php

namespace App\Http\Controllers;

use App\Mail\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Jobs\ContactUsJob; // Ensure this is imported

class ContactController extends Controller
{
    public function send(Request $request)
    {
        // Validation rules
        $validationRules = [
            'message' => 'required|min:1',
        ];

        // Validate incoming request data
        $request->validate($validationRules);

        // Fetching the authenticated user's username and email
        $user = Auth::user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Prepare the data to send in the email
        $data = [
            'username' => $user->username,
            'email' => $user->email,
            'message' => $request->message,
        ];

        // Dispatch the job to send the email
        ContactUsJob::dispatch($data);

        // Send email immediately (optional; you may want to rely on the job)
        Mail::to('lumpiajavarice@gmail.com')->send(new ContactUs($data));
        Log::info('Email sent successfully to: ' . $data['email']);

        return response()->json([
            'message' => 'Email sent successfully',
            'username' => $data['username'], // Use the variable from the data array
            'email' => $data['email'] // Use the variable from the data array
        ]);
    }
}
