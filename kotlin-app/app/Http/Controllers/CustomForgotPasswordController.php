<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomForgotPasswordController extends Controller
{
    // Check if email exists
    public function checkEmail(Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            return response()->json([
                'email_exists' => true,
                'message' => 'Email found. Sending OTP.'
            ]);
        } else {
            return response()->json([
                'email_exists' => false,
                'message' => 'Email not found.'
            ], 404); // Changed to 404 for not found
        }
    }

    // Send OTP to email
    public function sendOTP(Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);

        $otp = rand(100000, 999999); // Generate OTP
        $email = $request->email;

        // Save OTP to the database with timestamp
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            ['otp' => $otp, 'created_at' => Carbon::now()]
        );

        // Send OTP via email
        try {
            Mail::raw('Your OTP is: ' . $otp, function ($message) use ($email) {
                $message->to($email)
                    ->subject('Your OTP for Password Reset');
            });

            return response()->json(['message' => 'OTP sent to your email.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error sending OTP: ' . $e->getMessage()], 500);
        }
    }

    // Verify OTP
    public function verifyOTP(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        $otpRecord = DB::table('password_resets')->where('email', $request->email)->first();

        if ($otpRecord) {
            // Check if OTP is still valid (e.g., 15-minute expiration)
            $expiresAt = Carbon::parse($otpRecord->created_at)->addMinutes(15);
            if ($otpRecord->otp == $request->otp && Carbon::now()->lessThan($expiresAt)) {
                return response()->json([
                    'otp_valid' => true,
                    'message' => 'OTP verified.'
                ]);
            } elseif (Carbon::now()->greaterThanOrEqualTo($expiresAt)) {
                return response()->json([
                    'otp_valid' => false,
                    'message' => 'OTP expired.'
                ]);
            } else {
                return response()->json([
                    'otp_valid' => false,
                    'message' => 'Invalid OTP.'
                ]);
            }
        }

        return response()->json([
            'otp_valid' => false,
            'message' => 'Invalid OTP.'
        ], 404); // Changed to 404 for not found
    }

    public function resetPassword(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        // Check if OTP is verified
        $otpRecord = DB::table('password_resets')->where('email', $request->email)->first();
        if (!$otpRecord) {
            return response()->json(['message' => 'OTP verification required before resetting the password.'], 400);
        }
    
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
    
            // Remove the OTP record after successful password reset
            DB::table('password_resets')->where('email', $request->email)->delete();
    
            return response()->json(['message' => 'Password reset successful.'], 200);
        }
    
        return response()->json(['message' => 'User not found.'], 404);
    }
    
}
