<?php

namespace App\Http\Controllers;

use App\Mail\ContactUs;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Method to show the contact form (not currently used in your routes)
    public function showContactForm()
    {
        return view('contact'); // Return the view for the contact form
    }

    public function sendContactMessage(Request $request)
    {
        try {
            // Validate the incoming request data
            $data = $request->validate([
                'message' => 'required|min:1',
            ]);
    
            // Get the authenticated user's name and email
            $user = Auth::user();
    
            // Check if user is authenticated
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
    
            $username = $user->username; // Get the username
            $email = $user->email;       // Get the email
    
            // Save the message to the database including username and email
            Contact::create([
                'message' => $data['message'],
                'username' => $username,
                'email' => $email,
            ]);
    
            // Send the email
            Mail::to('lumpiajavarice@gmail.com')->send(new ContactUs($username, $email, $data['message']));
    
            return response()->json([
                'message' => 'Great! Successfully sent email and saved contact',
                'username' => $username,
                'email' => $email,
                'contact_message' => $data['message'],
            ], 200);
        } catch (\Swift_TransportException $e) {
            Log::error('Mail sending failed: ' . $e->getMessage());
            return response()->json(['message' => 'Sorry! Please try again later'], 500);
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }    
}
