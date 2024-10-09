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
Route::post('/login', [AuthManager::class, 'loginPost'])->name('login'); //ERROR 405
Route::post('/login', [AuthManager::class, 'loginPost'])->name('login.post'); //ERROR 500

Route::post('/register', [AuthManager::class, 'registrationPost'])->name('registration.post'); // POST for registration
Route::post('/logout', [AuthManager::class, 'logout'])->name('logout'); // POST for logout

// Get authenticated user
Route::middleware('auth')->get('/user', [AuthManager::class, 'getUser'])->name('auth.user');
Route::middleware('auth')->post('/contact/send', [ContactController::class, 'send'])->name('contact.send');
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show'); // GET for showing contact form

// Update the contact route to include the username and email
Route::middleware('auth')->post('/contact', [ContactController::class, 'send'])->name('contact.send');

// Task routes
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index'); // Get all tasks
Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('tasks.show'); // Get a specific task
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store'); // Create a new task
Route::put('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update'); // Update a task
Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy'); // Delete a task

// OTP routes
Route::post('/check-email', [CustomForgotPasswordController::class, 'checkEmail'])->name("check.email");
Route::post('/send-otp', [CustomForgotPasswordController::class, 'sendOTP'])->name("send.otp");
Route::post('/verify-otp', [CustomForgotPasswordController::class, 'verifyOTP'])->name("verify.otp");
Route::post('/password/reset', [CustomForgotPasswordController::class, 'resetPassword']);