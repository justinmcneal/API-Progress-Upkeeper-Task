<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TaskNotification;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TaskController extends Controller
{
    // Get all tasks
    public function index()
{
    try {
        $tasks = Task::all()->map(function ($task) {
            // Convert all relevant timestamps to Asia/Manila timezone
            $task->start_datetime = Carbon::parse($task->start_datetime)->timezone('Asia/Manila');
            $task->end_datetime = Carbon::parse($task->end_datetime)->timezone('Asia/Manila');
            $task->created_at = Carbon::parse($task->created_at)->timezone('Asia/Manila');
            $task->updated_at = Carbon::parse($task->updated_at)->timezone('Asia/Manila');
            return $task;
        });

        return response()->json($tasks, 200);
    } catch (\Exception $e) {
        Log::error('Error fetching tasks: ' . $e->getMessage());
        return response()->json([
            'message' => 'An error occurred while fetching tasks',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function show($id)
{
    try {
        $task = Task::findOrFail($id);
        // Convert all relevant timestamps to Asia/Manila timezone
        $task->start_datetime = Carbon::parse($task->start_datetime)->timezone('Asia/Manila');
        $task->end_datetime = Carbon::parse($task->end_datetime)->timezone('Asia/Manila');
        $task->created_at = Carbon::parse($task->created_at)->timezone('Asia/Manila');
        $task->updated_at = Carbon::parse($task->updated_at)->timezone('Asia/Manila');
        
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
            $task = Task::create($request->all());

             // Convert timestamps to Asia/Manila timezone before returning
            $task->start_datetime = Carbon::parse($task->start_datetime)->timezone('Asia/Manila');
            $task->end_datetime = Carbon::parse($task->end_datetime)->timezone('Asia/Manila');
            $task->created_at = Carbon::parse($task->created_at)->timezone('Asia/Manila');
            $task->updated_at = Carbon::parse($task->updated_at)->timezone('Asia/Manila');
            return response()->json($task, 201);
        } catch (ValidationException $e) {
            Log::error('Validation error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }

    // Update an existing task
    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->update($request->all());

            // Convert timestamps to Asia/Manila timezone before returning
            $task->start_datetime = Carbon::parse($task->start_datetime)->timezone('Asia/Manila');
            $task->end_datetime = Carbon::parse($task->end_datetime)->timezone('Asia/Manila');
            $task->created_at = Carbon::parse($task->created_at)->timezone('Asia/Manila');
            $task->updated_at = Carbon::parse($task->updated_at)->timezone('Asia/Manila');

            return response()->json($task, 200);
        } catch (ModelNotFoundException $e) {
            Log::warning("Task not found with ID: $id");
            return response()->json([
                'message' => 'Task not found',
                'error' => $e->getMessage(),
            ], 404);
        } catch (ValidationException $e) {
            Log::error('Validation error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred',
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