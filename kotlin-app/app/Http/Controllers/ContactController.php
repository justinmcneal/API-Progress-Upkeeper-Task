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

            // Prepare the data, including the user's username and email
            $data = [
                'username' => $user->username,
                'email' => $user->email,
                'message' => $request->message,
            ];

            // Save contact message to the database
            Contact::create($data);

            // Send the email
            Mail::to('lumpiajavarice@gmail.com')->send(new ContactUs($data));

            return response()->json(['message' => 'Great! Successfully sent email and saved contact'], 200);
        } catch (\Swift_TransportException $e) {
            // Email sending failed
            Log::error('Mail sending failed: ' . $e->getMessage());
            return response()->json(['message' => 'Sorry! Please try again later'], 500);
        } catch (\Exception $e) {
            // Catch any other general exception
            Log::error('An error occurred: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
