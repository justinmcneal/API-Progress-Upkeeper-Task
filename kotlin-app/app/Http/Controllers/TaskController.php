<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

// class TaskController extends Controller
// {
    /**
     * Display a listing of the resource.
     */
   // public function index()
    // {
        //
    // }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
        //
    // }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
        //
    // }

    /**
     * Display the specified resource.
     */
    // public function show(Task $task)
    // {
        //
    // }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(Task $task)
    // {
        //
    // }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Task $task)
    // {
        //
    // }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Task $task)
    // {
        //
    // }
// }

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


    // Create a new task
    // public function store(Request $request)
    // {
       // $request->validate([
         //   'task_name' => 'required|string|max:255',
         //   'start_datetime' => 'required|date|before:end_datetime',
         //   'end_datetime' => 'required|date|after:start_datetime',
        // ]);
        

        // $task = new Task($request->all());

        // Handle file upload if exists
        // if ($request->hasFile('attachment')) {
           // $path = $request->file('attachment')->store('attachments', 'public');
           // $task->attachment = $path;
        // }

        // $task->save();

        // return response()->json($task, 201);
    // }

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

