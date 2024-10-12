<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class AuthManager extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            try {
                // Create a Sanctum token
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'message' => 'Login Successful',
                    'user' => $user,
                    'token' => $token,
                ], 200);
            } catch (\Exception $e) {
                Log::error('Failed to generate token for user', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                return response()->json([
                    'message' => 'Failed to generate token',
                ], 500);
            }
        }

        return response()->json([
            'message' => 'Login Details Not Valid',
        ], 422);
    }

    public function registrationPost(Request $request) {
        try {
            $request->validate([
                'username' => 'required|max:50|unique:users',
                'email' => 'required|email|max:50|unique:users',
                'password' => 'required|confirmed',
            ]);

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'message' => 'Registration Success!',
                'user' => $user,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Validation Error',
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkEmail(Request $request) {
        $request->validate(['email' => 'required|email']);

        $emailExists = User::where('email', $request->email)->exists();

        if ($emailExists) {
            $otp = random_int(100000, 999999);
            session(['otp' => $otp, 'otp_expires_at' => now()->addMinutes(10)]);
            \Mail::to($request->email)->send(new \App\Mail\OtpMail($otp));

            return response()->json([
                'message' => 'Email exists, OTP sent',
                'email_exists' => true,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Email does not exist',
                'email_exists' => false,
            ], 404);
        }
    }

    public function verifyOtp(Request $request) {
        $request->validate(['otp' => 'required|numeric']);

        $sessionOtp = session('otp');
        $expiresAt = session('otp_expires_at');

        if ($sessionOtp == $request->otp && now()->lessThan($expiresAt)) {
            return response()->json(['message' => 'OTP Verified Successfully'], 200);
        } else {
            return response()->json(['message' => 'Invalid or expired OTP'], 400);
        }
    }

    public function logout() {
        // Invalidate the token
        $user = Auth::user();
        $user->tokens()->delete(); // Delete all tokens for the user

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function user(Request $request) //changed the name getUser to user
    {
        // Fetch the authenticated user
        return response()->json(Auth::user(), 200);
    }

    public function updateUsername(Request $request) {
        $request->validate([
            'username' => 'required|max:50', // Adjust validation as needed
        ]);

        $user = Auth::user();
        $user->username = $request->username;
        $user->save();

        return response()->json([
            'message' => 'Username updated successfully.',
            'user' => $user,
        ], 200);
    }
}
