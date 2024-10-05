<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthManager;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TaskController;



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

// Route::apiResource('tasks', TaskController::class);

// Route::get('/task', [TaskController::class, 'store'])->name('task.store');
// Route::post('/task', [TaskController::class, 'store'])->name('task.post');

// Define a GET route for rendering a form (or retrieving data)
//Route::get('/task', [TaskController::class, 'index'])->name('task.index');

// Define a POST route for creating a task
//Route::post('/task', [TaskController::class, 'store'])->name('task.store');

// routes/api.php

// Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
// Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
// Route::put('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update');
// Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');


Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');  // Get all tasks
Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('tasks.show');  // Get a specific task
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');  // Create a new task
Route::post('/tasks/update/{id}', [TaskController::class, 'update']);
Route::put('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update'); // Update a task
Route::patch('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.patch'); // Partially update a task
Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');  // Delete a task


