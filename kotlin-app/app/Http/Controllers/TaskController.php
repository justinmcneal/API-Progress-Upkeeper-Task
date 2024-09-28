<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Get all tasks
    public function index()
    {
        return Task::all();
    }

    // Get a specific task
    public function show(Task $task)
    {
        return response()->json($task, 200);
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
        $task = Task::findOrFail($id);

        $task->update($request->all());

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            $task->attachment = $path;
        }

        $task->save();

        return response()->json(['message' => 'Task updated successfully', 'task' => $task], 200);

    }

    // Delete a task
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(null, 204);
    }
}

