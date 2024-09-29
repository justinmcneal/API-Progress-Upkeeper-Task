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
                'task_name' => 'required|string|max:255|unique:tasks,task_name',
                'task_description' => 'required|max:255',
                'start_datetime' => 'required|date|before:end_datetime',
                'end_datetime' => 'required|date|after:start_datetime',
                'attachment' => 'nullable|file',
                'repeat_days' => 'nullable|array',
                'repeat_days.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            ], [
                'task_name.unique' => 'A task with this name already exists. Please choose a different name.',
            ]);

            $task = new Task($request->all());

            // Handle attachment upload
            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('attachments', 'public');
                $task->attachment = $path;  // Save the path to the task model
            }

            $task->save();

            // Handle repeat_days logic (if applicable)
            if ($request->filled('repeat_days')) {
                foreach ($request->repeat_days as $day) {
                    $newTask = $task->replicate();  // Create a copy of the task
                    $newTask->start_datetime = $this->calculateNewStartDate($request->start_datetime, $day);
                    $newTask->end_datetime = $this->calculateNewEndDate($request->end_datetime, $day);
                    $newTask->save();
                }
            }

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
                'task_description' => 'sometimes|required|max:255',
                'start_datetime' => 'sometimes|required|date|before:end_datetime',
                'end_datetime' => 'sometimes|required|date|after:start_datetime',
                'attachment' => 'nullable|file',
                'repeat_days' => 'nullable|array',
                'repeat_days.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            ]);

            // Update the task
            if ($request->has('task_name')) {
                $task->task_name = $request->task_name;
            }

            if ($request->has('task_description')) {
                $task->task_description = $request->task_description;
            }

            if ($request->has('start_datetime')) {
                $task->start_datetime = $request->start_datetime;
            }

            if ($request->has('end_datetime')) {
                $task->end_datetime = $request->end_datetime;
            }

            // Handle attachment update
            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('attachments', 'public');
                $task->attachment = $path;  // Update attachment path
            }

            $task->save();

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

    // Method to calculate new start date
    private function calculateNewStartDate($startDate, $day) 
    {
        // Convert to Carbon instance
        $carbonStartDate = \Carbon\Carbon::parse($startDate);
        $targetDay = \Carbon\Carbon::now()->next($day); // Get the next occurrence of the target day

        return $targetDay->setTime($carbonStartDate->hour, $carbonStartDate->minute);
    }

    // Method to calculate new end date
    private function calculateNewEndDate($endDate, $day) 
    {
        // Similar logic for end date
        $carbonEndDate = \Carbon\Carbon::parse($endDate);
        $targetDay = \Carbon\Carbon::now()->next($day); // Get the next occurrence of the target day

        return $targetDay->setTime($carbonEndDate->hour, $carbonEndDate->minute);
    }
}
