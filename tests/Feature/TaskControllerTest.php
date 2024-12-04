<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function manager_can_view_create_task_page()
    {
        $manager = User::factory()->create();
        $manager->assignRole('Manager');

        $response = $this->actingAs($manager)->get(route('tasks.create'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.tasks.create');
    }

    /** @test */
    public function non_manager_cannot_view_create_task_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('tasks.create'));

        $response->assertStatus(403);
    }

    /** @test */
    public function manager_can_store_a_task()
    {
        $manager = User::factory()->create();
        $manager->assignRole('Manager');

        $user = User::factory()->create();

        $response = $this->actingAs($manager)->post(route('tasks.store'), [
            'title' => 'Test Task',
            'description' => 'Test description',
            'priority' => 'high',
            'assigned_user' => $user->id,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test description',
            'priority' => 'high',
            'assigned_user' => $user->id,
        ]);
    }

    /** @test */
    public function assigned_user_can_update_task_status()
    {
        $task = Task::factory()->create([
            'status' => 'pending',
            'assigned_user' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->patch(route('tasks.update', $task), [
            'status' => 'completed',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /** @test */
    public function non_assigned_user_cannot_update_task_status()
    {
        $task = Task::factory()->create(['assigned_user' => User::factory()->create()->id]);

        $response = $this->actingAs($this->user)->patch(route('tasks.update', $task), [
            'status' => 'completed',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_view_all_tasks()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        Task::factory()->count(5)->create();

        $response = $this->actingAs($admin)->get(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.tasks.index');
        $response->assertViewHas('tasks');
    }

    /** @test */
    public function non_admin_cannot_view_all_tasks()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('tasks.index'));

        $response->assertStatus(403);
    }
}
