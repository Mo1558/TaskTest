<?php

namespace App\Http\Controllers;


use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // For manager to create and assign tasks
    public function create()
    {
        if (!auth()->user()->hasRole('Manager')) {
            return abort(403);
        }

        $users = User::all(); // Get all users to assign tasks
        return view('pages.tasks.create', compact('users'));
    }

    public function store(TaskRequest $request)
    {
        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
            'priority' => $request->priority,
            'assigned_user' => $request->assigned_user,
        ]);

        return redirect()->route('tasks.index');
    }

    // For users to update the status of tasks
    public function edit(Task $task)
    {
        if (auth()->user()->id != $task->assigned_user) {
            return abort(403);
        }

        return view('pages.tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:pending,in-progress,completed',
        ]);

        if (auth()->user()->id != $task->assigned_user) {
            return abort(403);
        }

        $task->update([
            'status' => $request->status,
            'completed_at' => $request->status == 'completed' ? now() : null,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
    }


    // For admin to view all tasks
    public function index()
    {
        if (!auth()->user()->hasRole('Admin')) {
            return abort(403);
        }

        $tasks = Task::with('user')->get();
        return view('pages.tasks.index', compact('tasks'));
    }
}
