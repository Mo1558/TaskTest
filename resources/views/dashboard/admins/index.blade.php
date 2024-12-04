@extends('layouts.layout')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container">
        <h1 class="mb-4">Admin Dashboard</h1>

        <div class="row">
            <!-- Total Users by Role -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Users by Role</div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach ($userRolesCount as $role)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ ucfirst($role->role) }}
                                    <span class="badge bg-primary">{{ $role->count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tasks by Status -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Tasks by Status</div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach ($tasksByStatus as $status)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ ucfirst($status->status) }}
                                    <span class="badge bg-info">{{ $status->count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tasks by Priority -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Tasks by Priority</div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach ($tasksByPriority as $priority)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ ucfirst($priority->priority) }}
                                    <span class="badge bg-warning">{{ $priority->count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Average Task Completion Time -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Average Task Completion Time</div>
                    <div class="card-body">
                        <h5>
                            {{ $averageCompletionTime ? gmdate('H:i:s', $averageCompletionTime) : 'No completed tasks yet' }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
