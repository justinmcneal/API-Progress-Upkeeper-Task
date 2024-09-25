<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthManager;
use App\Http\Controllers\ContactController;



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

// Route::post('/login', [AuthManager::class, 'loginPost']);
// Route::post('/register', [AuthManager::class, 'registrationPost']);
// Route::post('/logout', [AuthManager::class, 'logout']);

Route::get('/login', [AuthManager::class, 'login'])->name('login');
Route::post('/loginPost', [AuthManager::class, 'loginPost'])->name('login.post');
Route::get('/registration', [AuthManager::class, 'registration'])->name('registration');
Route::post('/registrationPost', [AuthManager::class, 'registrationPost'])->name('registration.post');
Route::get('/logout', [AuthManager::class, 'logout'])->name('logout');

Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::get('/send', [ContactController::class, 'show'])->name('contact.send');
Route::post('/send', [ContactController::class, 'send'])->name('contact.send');

