<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthManager;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::get('/login', [AuthManager::class, 'login'])->name('login');
Route::post('/loginPost', [AuthManager::class, 'loginPost'])->name('login.post');
Route::get('/registration', [AuthManager::class, 'registration'])->name('registration');
Route::post('/registrationPost', [AuthManager::class, 'registrationPost'])->name('registration.post');
Route::get('/logout', [AuthManager::class, 'logout'])->name('logout');

Route::get('/contact', [ContactController::class, 'show'])->name('contact.show'); 
Route::middleware('auth:api')->post('/contact/send', [ContactController::class, 'sendContactMessage'])->name('contact.send'); 

Route::get('/send', [ContactController::class, 'show'])->name('contact.send');
Route::post('/send', [ContactController::class, 'send'])->name('contact.send');

Route::middleware('auth:api')->post('/send-message', [UserController::class, 'sendContactMessage']);



Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');  // Get all tasks
Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('tasks.show');  // Get a specific task
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');  // Create a new task
Route::post('/tasks/update/{id}', [TaskController::class, 'update']);
Route::put('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update'); // Update a task
Route::patch('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.patch'); // Partially update a task
Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');  // Delete a task


