<?php

namespace Tests\Feature;

use Spatie\Permission\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class AdminDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_dashboard_with_correct_data()
    {
        // Create roles and assign users
        $managerRole = Role::factory()->create(['name' => 'Manager']);
        $userRole = Role::factory()->create(['name' => 'User']);

        $managers = User::factory()->count(3)->create()->each(function ($user) use ($managerRole) {
            $user->assignRole($managerRole);
        });

        $users = User::factory()->count(5)->create()->each(function ($user) use ($userRole) {
            $user->assignRole($userRole);
        });

        // Create tasks with varying statuses and priorities
        Task::factory()->count(4)->create(['status' => 'pending', 'priority' => 'high']);
        Task::factory()->count(2)->create(['status' => 'completed', 'priority' => 'medium', 'completed_at' => now()]);
        Task::factory()->count(3)->create(['status' => 'in-progress', 'priority' => 'low']);

        // Calculate expected data
        $expectedUserRolesCount = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name as role', DB::raw('COUNT(*) as count'))
            ->groupBy('roles.name')
            ->get();

        $expectedTasksByStatus = Task::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $expectedTasksByPriority = Task::select('priority', DB::raw('COUNT(*) as count'))
            ->groupBy('priority')
            ->get();

        $expectedAverageCompletionTime = Task::whereNotNull('completed_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, created_at, completed_at)) as avg_time'))
            ->value('avg_time');

        // Make the request
        $response = $this->actingAs($managers->first())->get(route('dashboard.admins.index'));

        // Assertions
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.admins.index');
        $response->assertViewHas('userRolesCount', $expectedUserRolesCount);
        $response->assertViewHas('tasksByStatus', $expectedTasksByStatus);
        $response->assertViewHas('tasksByPriority', $expectedTasksByPriority);
        $response->assertViewHas('averageCompletionTime', $expectedAverageCompletionTime);
    }
}
