<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Hash;

class TasksFeatureTest extends TestCase
{
    use RefreshDatabase;
    public function testCanCreateTask()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Task Description',
            'due_date' => now()->addDays(7),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'description', 'due_date']
            ]);
    }

    public function testCanGetAllTasks(){
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
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'due_date',
                        'is_overdue',
                    ]
                ],
                'last_page',
                'per_page',
                'total',
            ]
        ]);
    }

    public function testCanGetWithFiltersTasks(){
        $user = User::factory()->create();
        Task::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'doing',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/tasks?status=doing&q=Tes');

        $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'due_date',
                        'is_overdue',
                    ]
                ],
                'last_page',
                'per_page',
                'total',
            ]
        ]);
    }

    public function testCanGetTaskById(){
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/tasks/' . $task->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'description', 'due_date', 'status', 'is_overdue']
            ]);
    }

    public function testCanUpdateTask()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson('/api/tasks/' . $task->id, [
            'title' => 'Test Task Updated',
            'description' => 'Test Task Description Updated',
            'due_date' => now()->addDays(7),
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'description', 'due_date', 'status', 'is_overdue']
            ]);
    }

    public function testCanDeleteTask(){
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
            ]);
    }

    public function testCanRestoreTask(){
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'deleted_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson('/api/tasks/' . $task->id . '/restore');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'description', 'due_date', 'status', 'is_overdue']
            ]);
    }

    public function testCanForceDeleteTask(){
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'deleted_at' => now(),
        ]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/tasks/' . $task->id . '/force');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
            ]);
    }

    public function testCanCompletedTask(){
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->putJson('/api/tasks/' . $task->id . '/complete');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'description', 'due_date', 'status', 'is_overdue']
            ]);
    }
    
}
