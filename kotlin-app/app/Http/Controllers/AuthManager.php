<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthManager extends Controller
{
    public function loginPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Generate a token for the user (using Sanctum)
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login Successful',
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                ],
                'token' => $token
            ], 200);
        }

        return response()->json([
            'message' => 'Login Details Not Valid'
        ], 401);
    }
    
    public function registrationPost(Request $request)
    {
        try {
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

            return response()->json([
                'message' => 'Registration Success!',
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                ]
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

    public function logout()
    {
        Session::flush();
        Auth::logout();
        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}
