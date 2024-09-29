<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

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
            Log::info('Starting validation for storing task', ['request' => $request->all()]);

            $request->validate([
            'task_name' => 'required|string|max:255|unique:tasks,task_name,collation,utf8mb4_unicode_ci',
            'task_description' => 'required|max:255',
            'start_datetime' => 'required|date|before:end_datetime',
            'end_datetime' => 'required|date|after:start_datetime',
            'attachment' => 'nullable|file',
        ], [
            'task_name.unique' => 'A task with this name already exists. Please choose a different name.',
        ]);

        $task = new Task($request->all());

        // Handle attachment upload
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            $task->attachment = $path;
        }

        $task->save();

        return response()->json($task, 201);
    } catch (ValidationException $e) {
        Log::info('Validation failed: ' . json_encode($e->errors()));
        return response()->json([
            'message' => 'Validation error',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        Log::error('Error storing task: ' . $e->getMessage());
        return response()->json([
            'message' => 'An error occurred while storing the task',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    // Update a task
    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);

            $request->validate([
                'task_name' => 'sometimes|required|string|max:255',
                'start_datetime' => 'sometimes|required|date|before:end_datetime',
                'end_datetime' => 'sometimes|required|date|after:start_datetime',
                'attachment' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
            ]);

            $task->update($request->all());

            // Handle attachment update
            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('attachments', 'public');
                $task->attachment = $path;
            }

            return response()->json([
                'message' => 'Task updated successfully',
                'task' => $task,
            ], 200);
        } catch (ValidationException $e) {
            Log::info('Validation failed during update: ' . json_encode($e->errors()));
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            Log::warning("Task not found with ID: $id during update");
            return response()->json([
                'message' => 'Task not found',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error updating task with ID $id: " . $e->getMessage());
            return response()->json([
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

            return response()->json(null, 204);
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
