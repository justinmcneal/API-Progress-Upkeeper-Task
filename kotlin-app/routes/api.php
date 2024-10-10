<?php

namespace App\Http\Controllers\UserAuth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthManager;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CustomForgotPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them
| will be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::post('/login', [AuthManager::class, 'loginPost'])->name('login'); // Route for login
Route::post('/register', [AuthManager::class, 'registrationPost'])->name('registration.post'); // POST for registration
Route::post('/logout', [AuthManager::class, 'logout'])->name('logout'); // POST for logout

// Get authenticated user
Route::middleware('auth:api')->get('/user', [AuthManager::class, 'getUser'])->name('auth.user');

// Contact routes
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send'); // POST for sending contact form
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show'); // GET for showing contact form

// Task routes
Route::middleware('auth:api')->get('/tasks', [TaskController::class, 'index'])->name('tasks.index'); // Get all tasks
Route::middleware('auth:api')->get('/tasks/{id}', [TaskController::class, 'show'])->name('tasks.show'); // Get a specific task
Route::middleware('auth:api')->post('/tasks', [TaskController::class, 'store'])->name('tasks.store'); // Create a new task
Route::middleware('auth:api')->put('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update'); // Update a task
Route::middleware('auth:api')->delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy'); // Delete a task

// OTP routes
Route::post('/check-email', [CustomForgotPasswordController::class, 'checkEmail'])->name("check.email");
Route::post('/send-otp', [CustomForgotPasswordController::class, 'sendOTP'])->name("send.otp");
Route::post('/verify-otp', [CustomForgotPasswordController::class, 'verifyOTP'])->name("verify.otp");
Route::post('/password/reset', [CustomForgotPasswordController::class, 'resetPassword']);
