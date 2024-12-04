@extends('layouts.layout')

@section('title', 'Create Task')

@section('content')

    <form method="POST" action="{{ route('tasks.store') }}">
        @csrf
        <div class="form-group col-6">
            <label for="title">Task Title</label>
            <input type="text" class="form-control" name="title" required>
        </div>
        <div class="form-group col-6">
            <label for="description">Description</label>
            <textarea class="form-control" name="description" required></textarea>
        </div>
        <div class="form-group col-6">
            <label for="priority">Priority</label>
            <select class="form-control" name="priority" required>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select>
        </div>
        <div class="form-group col-6">
            <label for="assigned_user">Assign User</label>
            <select class="form-control" name="assigned_user">
                <option value="">None</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" style="margin-top: 10px" class="btn btn-primary">Create Task</button>
    </form>

@endsection
