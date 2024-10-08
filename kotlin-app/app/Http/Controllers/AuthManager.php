<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthManager extends Controller
{
    public function loginPost(Request $request) {
        // Validate requests
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean', // Add this line to validate remember field
        ]);
    
        $credentials = $request->only('email', 'password');
    
        // Check for remember token
        $remember = $request->has('remember'); // Check if remember is checked
    
        if (Auth::attempt($credentials, $remember)) { // Pass $remember to attempt()
            // Get user
            $user = Auth::user();
    
            // Ensure the user model is being used when creating the token
            if ($user instanceof \App\Models\User) {
                try {
                    $token = $user->createToken('auth_token')->plainTextToken;
    
                    // Return a JSON response with a success message, user info, and token
                    return response()->json([
                        'message' => 'Login Successful',
                        'user' => $user, // Optionally include user info
                        'token' => $token
                    ], 200);
                } catch (\Exception $e) {
                    // Log the error and return a JSON response with an error message
                    Log::error('Failed to generate token for user', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                    return response()->json([
                        'message' => 'Failed to generate token',
                    ], 500);
                }
            }
    
            return response()->json([
                'message' => 'User instance not found',
            ], 401);
        }
    
        // Return a JSON response with an error message
        return response()->json([
            'message' => 'Login Details Not Valid',
        ], 422);
    }    
    
    public function registrationPost(Request $request) {
        try {
            // Validate the request data
            $request->validate([
                'username' => 'required|max:50|unique:users',
                'email' => 'required|email|max:50|unique:users',
                'password' => 'required|confirmed'
            ]);
    
            // Create the user
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password) // Use Hash::make to encrypt the password
            ]);
    
            // Return a JSON response with success message and user data
            return response()->json([
                'message' => 'Registration Success!',
                'user' => $user
            ], 201)->header('Content-Type', 'application/json');
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return a JSON response with validation error messages
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Validation Error'
            ], 422)->header('Content-Type', 'application/json');
    
        } catch (\Exception $e) {
            // Return a JSON response with a generic error message
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500)->header('Content-Type', 'application/json');
        }
    }

    public function checkEmail(Request $request)
{
    // Validate the email field
    $request->validate([
        'email' => 'required|email'
    ]);

    // Check if the email exists in the users table
    $emailExists = User::where('email', $request->email)->exists();

    if ($emailExists) {
        // Generate a random 6-digit OTP
        $otp = random_int(100000, 999999);

        // Save the OTP to the database (or session/cache)
        // Example: Storing in the session (for simplicity)
        session(['otp' => $otp, 'otp_expires_at' => now()->addMinutes(10)]);

        // Send OTP via email
        \Mail::to($request->email)->send(new \App\Mail\OtpMail($otp));

        return response()->json([
            'message' => 'Email exists, OTP sent',
            'email_exists' => true
        ], 200);
    } else {
        return response()->json([
            'message' => 'Email does not exist',
            'email_exists' => false
        ], 404);
    }
}


public function verifyOtp(Request $request)
{
    $request->validate([
        'otp' => 'required|numeric',
    ]);

    // Retrieve OTP and expiration time from session
    $sessionOtp = session('otp');
    $expiresAt = session('otp_expires_at');

    // Check if OTP is valid and not expired
    if ($sessionOtp == $request->otp && now()->lessThan($expiresAt)) {
        // OTP is correct
        return response()->json([
            'message' => 'OTP Verified Successfully',
        ], 200);
    } else {
        // OTP is incorrect or expired
        return response()->json([
            'message' => 'Invalid or expired OTP',
        ], 400);
    }
}   

    public function logout() {
        Session::flush();
        Auth::logout();
        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}
