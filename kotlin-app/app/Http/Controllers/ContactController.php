<?php

namespace App\Http\Controllers;

use App\Mail\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Jobs\ContactUsJob; // Make sure this is imported

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validationRules = [
            'message' => 'required|min:1',
        ];
    
        // Fetching the authenticated user's username and email
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = [
            'username' => $user->username,
            'email' => $user->email,
            'message' => $request->message,
        ];

        Mail::to('lumpiajavarice@gmail.com')->send(new ContactUs($data));
        Log::info('Email sent successfully to: ' . $data['email']);
    
        try {
            // Validate incoming request data
            $request->validate($validationRules);
            
            // Dispatch the job to send the email
            ContactUsJob::dispatch($data);
            
            return response()->json(['message' => 'Great! Your message is being sent'], 200);
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
