<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Total number of users by role
        $userRolesCount = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name as role', DB::raw('COUNT(*) as count'))
            ->groupBy('roles.name')
            ->get();

        // Total tasks grouped by status
        $tasksByStatus = Task::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Total tasks grouped by priority
        $tasksByPriority = Task::select('priority', DB::raw('COUNT(*) as count'))
            ->groupBy('priority')
            ->get();

        // Average task completion time
        $averageCompletionTime = Task::whereNotNull('completed_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, created_at, completed_at)) as avg_time'))
            ->value('avg_time');

        return view('dashboard.admins.index', [
            'userRolesCount' => $userRolesCount,
            'tasksByStatus' => $tasksByStatus,
            'tasksByPriority' => $tasksByPriority,
            'averageCompletionTime' => $averageCompletionTime,
        ]);
    }
}
