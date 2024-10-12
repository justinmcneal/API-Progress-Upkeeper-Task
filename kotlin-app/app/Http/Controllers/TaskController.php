<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum'); // Ensuring that the user is authenticated using Sanctum
    }

    // Get all tasks for the authenticated user
    public function index(): \Illuminate\Http\JsonResponse
    {
        try {
            $tasks = Auth::user()->tasks; // Fetch tasks for the authenticated user
            return response()->json($tasks, 200);
        } catch (\Exception $e) {
            return $this->handleError('Error fetching tasks', $e);
        }
    }

    // Get a specific task
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            return response()->json($task, 200);
        } catch (ModelNotFoundException $e) {
            Log::warning("Task not found with ID: $id");
            return response()->json(['message' => 'Task not found'], 404);
        } catch (\Exception $e) {
            return $this->handleError("Error fetching task with ID $id", $e);
        }
    }

    // Store a new task
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'task_name' => 'required|string|max:255|unique:tasks,task_name',
                'task_description' => 'required|max:255',
                'start_datetime' => 'required|date|before:end_datetime',
                'end_datetime' => 'required|date|after:start_datetime',
                'repeat_days' => 'nullable|array', // Make repeat_days optional
                'repeat_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
                'category' => 'required|string|in:Home,Personal,Work,School', // Add validation rule for category
            ], [
                'task_name.unique' => 'A task with this name already exists. Please choose a different name.',
                'category.required' => 'The category field is required.',
                'category.in' => 'The category must be one of the following: Home, Personal, Work, or School.'
            ]);

            $task = Task::create([
                'task_name' => $request->task_name,
                'task_description' => $request->task_description,
                'start_datetime' => $request->start_datetime,
                'end_datetime' => $request->end_datetime,
                'repeat_days' => $request->repeat_days ?? null, // Set to null if repeat_days is not provided
                'category' => $request->category, // Assign category
                'user_id' => Auth::id(), // Using authenticated user's ID
            ]);

            return response()->json([
                'message' => 'Task created successfully',
                'task' => $task,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return $this->handleError('An error occurred while storing the task', $e);
        }
    }

    // Update an existing task
    public function update(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

            $validatedData = $request->validate([
                'task_name' => 'sometimes|string|max:255',
                'task_description' => 'sometimes|max:255',
                'start_datetime' => 'sometimes|date|before:end_datetime',
                'end_datetime' => 'sometimes|date|after:start_datetime',
                'repeat_days' => 'nullable|array', // Make repeat_days optional
                'repeat_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
                'category' => 'required|string|in:Home,Personal,Work,School' // Make category required
            ]);

            $task->fill($validatedData);
            $task->save();

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully',
                'updated_task' => $task,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Task not found'], 404);
        } catch (\Exception $e) {
            return $this->handleError('An error occurred while updating the task', $e);
        }
    }

    // Delete a task
    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            $task->delete();

            return response()->json(['success' => true, 'message' => 'Task deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Task not found'], 404);
        } catch (\Exception $e) {
            return $this->handleError('An error occurred while deleting the task', $e);
        }
    }

    // Handle errors
    private function handleError(string $message, \Exception $exception): \Illuminate\Http\JsonResponse
    {
        Log::error($message, ['error' => $exception->getMessage()]);
        return response()->json(['message' => 'An error occurred', 'error' => $exception->getMessage()], 500);
    }
}
