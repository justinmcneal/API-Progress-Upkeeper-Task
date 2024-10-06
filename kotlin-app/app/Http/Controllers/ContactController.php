<?php

namespace App\Http\Controllers;

use App\Mail\ContactUs;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        try {
            // Validate incoming request data (only message is required since name and email will come from auth)
            $data = $request->validate([
                'message' => 'required|min:1',
            ]);

            // Get authenticated user's name and email
            $user = auth()->user();
            $data['username'] = $user->name; // Assuming the user's name field is 'name'
            $data['email'] = $user->email;   // Assuming the user's email field is 'email'

            // Save contact message to the database
            Contact::create($data);

            // Send the email
            Mail::to('lumpiajavarice@gmail.com')->send(new ContactUs($data));

            return response()->json(['message' => 'Great! Successfully sent email and saved contact'], 200);
        } catch (\Swift_TransportException $e) {
            // Email sending failed (due to server issue, SMTP misconfiguration, etc.)
            Log::error('Mail sending failed: ' . $e->getMessage());
            return response()->json(['message' => 'Sorry! Please try again later'], 500);
        } catch (\Exception $e) {
            // Catch any other general exception
            Log::error('An error occurred: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
