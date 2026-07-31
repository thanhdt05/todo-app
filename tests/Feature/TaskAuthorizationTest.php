<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_only_sees_own_tasks(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownTask = Task::factory()->for($user)->create();
        Task::factory()->for($otherUser)->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/tasks');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ownTask->id);
    }

    public function test_manager_can_update_another_user_task(): void
    {
        $manager = User::factory()->manager()->create();
        $owner = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        $this
            ->actingAs($manager, 'sanctum')
            ->patchJson("/api/tasks/{$task->id}", [
                'title' => 'Updated by manager',
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated by manager');
    }

    public function test_manager_cannot_delete_another_user_task(): void
    {
        $manager = User::factory()->manager()->create();
        $owner = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        $this
            ->actingAs($manager, 'sanctum')
            ->deleteJson("/api/tasks/{$task->id}")
            ->assertForbidden()
            ->assertJsonPath(
                'message',
                'Bạn không có quyền xóa công việc này'
            );

        $this->assertNotSoftDeleted($task);
    }

    public function test_user_cannot_update_another_user_task(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $task = Task::factory()->for($owner)->create([
            'title' => 'Original title',
        ]);

        $this
            ->actingAs($user, 'sanctum')
            ->patchJson("/api/tasks/{$task->id}", [
                'title' => 'Unauthorized update',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Original title',
        ]);
    }

    public function test_only_admin_can_force_delete_task(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->create();

        $task = Task::factory()
            ->for($owner)
            ->create(['deleted_at' => now()]);

        $this
            ->actingAs($admin, 'sanctum')
            ->deleteJson("/api/tasks/{$task->id}/force")
            ->assertOk();

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_regular_user_cannot_force_delete_own_task(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()
            ->for($user)
            ->create(['deleted_at' => now()]);

        $this
            ->actingAs($user, 'sanctum')
            ->deleteJson("/api/tasks/{$task->id}/force")
            ->assertForbidden();
    }

    public function test_bulk_restore_is_all_or_nothing_for_authorization(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownTask = Task::factory()
            ->for($user)
            ->create(['deleted_at' => now()]);

        $otherTask = Task::factory()
            ->for($otherUser)
            ->create(['deleted_at' => now()]);

        $this
            ->actingAs($user, 'sanctum')
            ->putJson('/api/tasks/bulk-restore', [
                'ids' => [$ownTask->id, $otherTask->id],
            ])
            ->assertForbidden();

        $this->assertSoftDeleted($ownTask);
        $this->assertSoftDeleted($otherTask);
    }
}
