<?php

namespace App\Http\Controllers;

use App\Jobs\ContactUsJob;
use App\Mail\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        Log::info('ContactController send method called'); // Log when the method is called

        try {
            // Log the incoming request data for debugging
            Log::info('Incoming request data: ', $request->all());

            // Validation rules
            $validationRules = [
                'message' => 'required|min:1|max:1000', // Added max length for message
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

            // Optionally send the email immediately
            Mail::to('lumpiajavarice@gmail.com')->send(new ContactUs($data));
            Log::info('Email sent successfully to: ' . $data['email']);

            // Dispatch the job to send the email (in case you want to send it asynchronously)
            ContactUsJob::dispatch($data);

            // Return a success response with a JSON object
            return response()->json([
                'message' => 'Contact form submitted successfully',
                'data' => [
                    'username' => $user->username,
                    'email' => $user->email,
                ],
            ]);

        } catch (\Throwable $e) {
            // Log the error for debugging
            Log::error('Error sending email: ' . $e->getMessage());

            // Return a structured error response
            return response()->json([
                'message' => 'Failed to send email',
                'error' => 'An unexpected error occurred. Please try again later.',
            ], 500); // 500 Internal Server Error status code
        }
    }
}
