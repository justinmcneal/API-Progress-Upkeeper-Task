<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification; // Import the Notification facade
use App\Notifications\TaskNotification; // Import your custom notification class
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // Get all tasks
    public function index()
    {
        try {
            $tasks = Task::all();
            return response()->json($tasks, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching tasks: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching tasks',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Get a specific task
    public function show($id)
    {
        try {
            $task = Task::findOrFail($id);
            return response()->json($task, 200);
        } catch (ModelNotFoundException $e) {
            Log::warning("Task not found with ID: $id");
            return response()->json([
                'message' => 'Task not found',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error fetching task with ID $id: " . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching the task',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Store a new task
    public function store(Request $request)
    {

        try {
            // Validate the request
            $request->validate([
                'task_name' => 'required|string|max:255|unique:tasks,task_name',
                'task_description' => 'required|max:255',
                'start_datetime' => 'required|date|before:end_datetime',
                'end_datetime' => 'required|date|after:start_datetime',
                'repeat_days' => 'nullable|array',
                'repeat_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'
            ], [
                'task_name.unique' => 'A task with this name already exists. Please choose a different name.',
            ]);

            // Fetch the authenticated user
            $user = Auth::user();

            if ($user) {
                // Create and save the task for the authenticated user
                $task = new Task([
                    'task_name' => $request->task_name,
                    'task_description' => $request->task_description,
                    'start_datetime' => $request->start_datetime,
                    'end_datetime' => $request->end_datetime,
                    'repeat_days' => $request->repeat_days,
                    'user_id' => $user->id  // Associate the task with the authenticated user
                ]);

                $task->save();

                return response()->json([
                    'message' => 'Task created successfully',
                    'task' => $task
                ], 201);
            } else {
                return response()->json([
                    'message' => 'User not authenticated',
                ], 401);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while storing the task',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Update an existing task
    public function update(Request $request, $id)
    {

        try {
            $task = Task::findOrFail($id);

            // Validate input
            $validatedData = $request->validate([
                'task_name' => 'sometimes|string|max:255',
                'task_description' => 'sometimes|max:255',
                'start_datetime' => 'sometimes|date|before:end_datetime',
                'end_datetime' => 'sometimes|date|after:start_datetime',
                'repeat_days' => 'nullable|array',
                'repeat_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'
            ]);

            // Manually merge validated data into the task
            $task->fill($validatedData);

            // Save the updated task
            $task->save();

            // Check if user is authenticated before sending notification
            $user = auth()->user();
            if ($user) {
                Notification::send($user, new TaskNotification($task));
            }

            // Return the updated task with timestamps converted to Asia/Manila timezone
            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully',
                'updated_task' => [
                    'id' => $task->id,
                    'task_name' => $task->task_name,
                    'task_description' => $task->task_description,
                    'start_datetime' => $task->start_datetime->setTimezone('Asia/Manila')->toDateTimeString(),
                    'end_datetime' => $task->end_datetime->setTimezone('Asia/Manila')->toDateTimeString(),
                    'repeat_days' => $task->repeat_days,
                    'isChecked' => $task->isChecked,
                    'created_at' => $task->created_at->setTimezone('Asia/Manila')->toDateTimeString(),
                    'updated_at' => $task->updated_at->setTimezone('Asia/Manila')->toDateTimeString()
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the task',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Delete a task
    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->delete();

            return response()->json([
                'message' => 'Task deleted successfully',
                'task' => $task,
            ], 200);
        } catch (ModelNotFoundException $e) {
            Log::warning("Task not found with ID: $id during deletion");
            return response()->json([
                'message' => 'Task not found',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error deleting task with ID $id: " . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while deleting the task',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Fetch tasks for the authenticated user
    public function userTasks()
    {
        try {
            // Fetch tasks associated with the authenticated user
            $tasks = Auth::user()->tasks;
            return response()->json($tasks, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching user tasks: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while fetching tasks',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
        

    public function __construct()
{
    $this->middleware('auth:api');
}

}
