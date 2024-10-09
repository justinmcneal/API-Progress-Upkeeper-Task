<?php

namespace App\Http\Controllers;

use App\Mail\ContactUs;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Validator;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function send(Request $request)
    {

        // return response()->json(['message' => 'this is just a test ']);
        $validationRules = [
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
            $user = Auth::user();
    
            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            // Fetch the username and email
            $username = $user->username;
            $email = $user->email;
    
            // Save contact message to the database
            $contact = Contact::create([
                'username' => $username, // Assign username
                'email' => $email, // Assign emailv
                'message' => $request->message, // Only message is fillable
            ]);
    
            // Assign username and email from the authenticated user
            // $contact->username = $user->username; // Assign username
            // $contact->email = $user->email; // Assign email
            // $contact->save(); 
    
            // Send the email
            Mail::to('lumpiajavarice@gmail.com')->send(new ContactUs($user->username, $user->email, $request->message));
    
            return response()->json(['message' => 'Great! Successfully sent email and saved contact'], 200);
        } catch (\Swift_TransportException $e) {
            Log::error('Mail sending failed: ' . $e->getMessage());
            return response()->json(['message' => 'Sorry! Please try again later'], 500);
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    
        
}
