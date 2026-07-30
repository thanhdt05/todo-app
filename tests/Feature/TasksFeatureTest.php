<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TasksFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_task()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Task Description',
            'priority' => 'high',
            'due_date' => now()->addDays(7),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'description', 'priority', 'due_date'],
            ])
            ->assertJsonPath('data.priority', 'high');
    }

    public function test_can_get_all_tasks()
    {
        $user = User::factory()->create();
        Task::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'priority',
                        'due_date',
                        'is_overdue',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
                'links',
            ]);
    }

    public function test_can_get_with_filters_tasks()
    {
        $user = User::factory()->create();

        Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Learn Laravel',
            'status' => 'doing',
            'priority' => 'high',
        ]);

        Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Learn Vue.js',
            'status' => 'doing',
            'priority' => 'low',
        ]);

        Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Learn PHP',
            'status' => 'todo',
            'priority' => 'medium',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/tasks?status=doing&keyword=LARAVEL');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Learn Laravel');
    }

    public function test_can_filter_by_priority()
    {
        $user = User::factory()->create();

        Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'High Priority Task',
            'priority' => 'high',
        ]);

        Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Low Priority Task',
            'priority' => 'low',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/tasks?priority=high');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'High Priority Task');
    }

    public function test_can_sort_tasks_by_due_date()
    {
        $user = User::factory()->create();

        $laterTask = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Later Task',
            'due_date' => now()->addDays(10),
        ]);

        $earlierTask = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Earlier Task',
            'due_date' => now()->addDays(2),
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/tasks?sort=due_date&direction=asc');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.id', $earlierTask->id)
            ->assertJsonPath('data.1.id', $laterTask->id);
    }

    public function test_can_bulk_restore_trashed_tasks()
    {
        $user = User::factory()->create();
        $task1 = Task::factory()->create(['user_id' => $user->id, 'deleted_at' => now()]);
        $task2 = Task::factory()->create(['user_id' => $user->id, 'deleted_at' => now()]);

        $response = $this->actingAs($user, 'sanctum')->putJson('/api/tasks/bulk-restore', [
            'ids' => [$task1->id, $task2->id],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.restored_count', 2);

        $this->assertDatabaseHas('tasks', ['id' => $task1->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('tasks', ['id' => $task2->id, 'deleted_at' => null]);
    }

    public function test_can_get_task_by_id()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/tasks/'.$task->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'description', 'due_date', 'status', 'priority', 'is_overdue'],
            ]);
    }

    public function test_can_get_all_trashed_tasks()
    {
        $user = User::factory()->create();
        Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'deleted_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/tasks/trashed');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'priority',
                        'due_date',
                        'is_overdue',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
                'links',
            ]);
    }

    public function test_can_update_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson('/api/tasks/'.$task->id, [
            'title' => 'Test Task Updated',
            'description' => 'Test Task Description Updated',
            'priority' => 'high',
            'due_date' => now()->addDays(7),
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'description', 'due_date', 'status', 'priority', 'is_overdue'],
            ])
            ->assertJsonPath('data.priority', 'high');
    }

    public function test_can_delete_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/tasks/'.$task->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
            ]);
    }

    public function test_can_restore_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'deleted_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson('/api/tasks/'.$task->id.'/restore');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'description', 'due_date', 'status', 'priority', 'is_overdue'],
            ]);
    }

    public function test_can_force_delete_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'deleted_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/tasks/'.$task->id.'/force');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
            ]);
    }

    public function test_can_completed_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson('/api/tasks/'.$task->id.'/complete');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'description', 'due_date', 'status', 'priority', 'is_overdue'],
            ]);
    }

    public function test_task_without_due_date_is_not_overdue_when_completed()
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'due_date' => null,
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson('/api/tasks/'.$task->id.'/complete');

        $response->assertStatus(200)
            ->assertJsonPath('data.is_overdue', false);
    }

    public function test_allows_changing_tasks_per_page()
    {
        $user = User::factory()->create();

        Task::factory()->count(20)->for($user)->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/tasks?per_page=10');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.per_page', 10);
    }

    public function test_can_update_overdue_task_title_without_changing_overdue_due_date()
    {
        $user = User::factory()->create();
        $overdueTask = Task::factory()->for($user)->create([
            'due_date' => now()->subDays(5)->toDateString(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->patchJson("/api/tasks/{$overdueTask->id}", [
            'title' => 'Updated Title for Overdue Task',
            'due_date' => $overdueTask->due_date,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Title for Overdue Task');
    }
}
