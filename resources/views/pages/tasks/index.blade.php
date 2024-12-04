@extends('layouts.layout')

@section('title', 'Tasks')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Tasks</h1>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Task
        </a>
    </div>

    <table class="table table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Assigned User</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tasks as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ Str::limit($task->description, 50, '...') }}</td>
                    <td>
                        <span class="badge bg-{{ $task->status_class }}">{{ ucfirst($task->status) }}</span>
                    </td>
                    <td>{{ ucfirst($task->priority) }}</td>
                    <td>{{ $task->user->name ?? 'Not assigned' }}</td>
                    <td>
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-warning" title="Edit">
                            Edit
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No tasks found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
