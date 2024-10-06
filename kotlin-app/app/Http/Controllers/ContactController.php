<?php

namespace App\Http\Controllers;

use App\Mail\ContactUs;
use App\Models\Contact; // Import the Contact model
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
        $validationRules = [
            'username' => 'required|max:255',
            'email' => 'required|email',
            'message' => 'required|min:1',
        ];
    
        $validator = Validator::make($request->all(), $validationRules);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()->all(),
            ], 422);
        }
    
        try {
            // Validate incoming request data
            $data = $request->validate($validationRules);
    
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