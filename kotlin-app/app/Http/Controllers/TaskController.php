<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification; // Import the Notification facade
use App\Notifications\TaskNotification; // Import your custom notification class
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
        \Log::info('Incoming request data:', $request->all());
        \Log::info('Incoming request headers:', $request->headers->all());

        $request->validate([
            'task_name' => 'required|string|max:255|unique:tasks,task_name',
            'task_description' => 'required|max:255',
            'start_datetime' => 'required|date|before:end_datetime',
            'end_datetime' => 'required|date|after:start_datetime',
            'attachment' => 'sometimes|file',
            'repeat_days' => 'nullable|array',
            'repeat_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'
        ], [
            'task_name.unique' => 'A task with this name already exists. Please choose a different name.',
        ]);

        $task = new Task($request->all());

         // Handle the file upload if there's an attachment
         if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            $task->attachment = $path;
        }

        $task->repeat_days = $request->input('repeat_days');
        $task->save();

        // Check if user is authenticated before sending notification
        $user = auth()->user();
        if ($user) {
            Notification::send($user, new TaskNotification($task));
        }

        return response()->json($task, 201);
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

            // Handle the file upload if there's an attachment
        if ($request->hasFile('attachment')) {
            // Delete the old file if it exists
            if ($task->attachment) {
                Storage::disk('public')->delete($task->attachment);
            }

            // Store the new attachment and update the task
            $path = $request->file('attachment')->store('attachments', 'public');
            $task->attachment = $path;
        }

            $task->repeat_days = $request->input('repeat_days', $task->repeat_days);
            $task->save();

            // Check if user is authenticated before sending notification
            $user = auth()->user();
            if ($user) {
                Notification::send($user, new TaskNotification($task));
            }

            return response()->json([
                'message' => 'Task updated successfully',
                'task' => $task,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Task not found',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the task',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateAttachment(Request $request, $id)
    {
        try {
            // Log incoming request data for debugging
            \Log::info('Request data:', $request->all());
            \Log::info('Files:', $request->file());
    
            // Find the task by ID
            $task = Task::findOrFail($id);
    
            // Validate that the request has a file
            $request->validate([
                'attachment' => 'sometimes|file',
            ]);
    
            // Handle the file upload
        if ($request->hasFile('attachment')) {
            // Delete the old attachment if it exists
            if ($task->attachment) {
                Storage::disk('public')->delete($task->attachment);
            }

            // Store the new attachment
            \Log::info('File is present');
            $path = $request->file('attachment')->store('attachments', 'public');
            $task->attachment = $path;
            $task->save();
    
                // Return success response
                return response()->json([
                    'message' => 'Attachment updated successfully',
                    'task' => $task,
                ], 200);
            } else {
                \Log::info('No file detected');
                return response()->json([
                    'message' => 'No file provided',
                ], 400);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Task not found',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the attachment',
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
}
