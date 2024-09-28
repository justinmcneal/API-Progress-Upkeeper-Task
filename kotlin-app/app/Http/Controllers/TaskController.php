<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Get all tasks
    public function index()
    {
        try {
            $tasks = Task::all();
            return response()->json($tasks, 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching tasks: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching tasks'], 500);
        }
    }

    // Get a specific task
    public function show(Task $task)
    {
        try {
            return response()->json($task, 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching task: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching task'], 500);
        }
    }
    public function store(Request $request)
{
    try {
        $request->validate([
            'task_name' => 'required|string|max:255',
            'start_datetime' => 'required|date|before:end_datetime',
            'end_datetime' => 'required|date|after:start_datetime',
        ]);

        $task = new Task($request->all());

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            $task->attachment = $path;
        }

        $task->save();

        return response()->json($task, 201);
    } catch (\Exception $e) {
        \Log::error('Error storing task: ' . $e->getMessage());
        return response()->json(['error' => 'Error storing task'], 500);
    }
}


    // Update a task
    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);

            $task->update($request->all());

            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('attachments', 'public');
                $task->attachment = $path;
            }

            $task->save();

            return response()->json(['message' => 'Task updated successfully', 'task' => $task], 200);
        } catch (\Exception $e) {
            \Log::error('Error updating task: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating task'], 500);
        }
    }

    // Delete a task
    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            \Log::error('Error deleting task: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting task'], 500);
        }
    }
}

