<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum'); // Ensuring that the user is authenticated using Sanctum
    }

    // Get all tasks for the authenticated user
    public function index(Request $request)
    {
        $tasks = Task::where('user_id', $request->user()->id)->get();  // Adjust this based on your logic
        return response()->json($tasks);
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
            'end_date' => 'required|date|after_or_equal:today',
            'end_time' => 'required|date_format:H:i',
            'repeat_days' => 'nullable|array',
            'repeat_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'category' => 'required|string|in:Home,Personal,Work,Wishlist',
        ], [
            'task_name.unique' => 'A task with this name already exists. Please choose a different name.',
        ]);

        // Combine date and time for full comparison
        $fullEndDateTime = Carbon::parse($request->end_date . ' ' . $request->end_time);

        if ($fullEndDateTime->isPast()) {
            return response()->json(['message' => 'The end date and time must be in the future.'], 422);
        }

        $task = Task::create([
            'task_name' => $request->task_name,
            'task_description' => $request->task_description,
            'end_date' => $request->end_date,
            'end_time' => $request->end_time,
            'repeat_days' => $request->repeat_days ?? null,
            'category' => $request->category,
            'user_id' => Auth::id(),
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
            'end_date' => 'required|date|after_or_equal:today',
            'end_time' => 'required|date_format:H:i',
            'repeat_days' => 'nullable|array',
            'repeat_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'category' => 'required|string|in:Home,Personal,Work,Wishlist',
        ]);

        // Combine date and time for full comparison
        $fullEndDateTime = Carbon::parse($request->end_date . ' ' . $request->end_time);

        if ($fullEndDateTime->isPast()) {
            return response()->json(['message' => 'The end date and time must be in the future.'], 422);
        }

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

    // Mark a task as complete or incomplete
    public function complete(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            // Find the task for the authenticated user
            $task = Task::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

            // Toggle the is_checked status
            $task->is_checked = !$task->is_checked;
            $task->save();

            return response()->json([
                'success' => true,
                'message' => 'Task completion status updated successfully',
                'task' => $task,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Task not found'], 404);
        } catch (\Exception $e) {
            return $this->handleError('An error occurred while updating task completion status', $e);
        }
    }


    // Handle errors
    private function handleError(string $message, \Exception $exception): \Illuminate\Http\JsonResponse
    {
        Log::error($message, ['error' => $exception->getMessage()]);
        return response()->json(['message' => 'An error occurred', 'error' => $exception->getMessage()], 500);
    }
}
