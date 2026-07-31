<?php

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

test('cannot access or modify another user task', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $taskB = Task::factory()->create([
        'user_id' => $userB->id,
        'title' => 'Original Title',
        'status' => 'todo',
    ]);

    actingAs($userA, 'sanctum')
        ->getJson("/api/tasks/{$taskB->id}")
        ->assertForbidden();

    actingAs($userA, 'sanctum')
        ->putJson("/api/tasks/{$taskB->id}", [
            'title' => 'Hacked Title',
        ])
        ->assertForbidden();

    expect($taskB->fresh()->title)->toBe('Original Title');

    actingAs($userA, 'sanctum')
        ->putJson("/api/tasks/{$taskB->id}/complete")
        ->assertForbidden();

    expect($taskB->fresh()->status)->toBe(TaskStatus::TODO);

    actingAs($userA, 'sanctum')
        ->deleteJson("/api/tasks/{$taskB->id}")
        ->assertForbidden();

    assertDatabaseHas('tasks', [
        'id' => $taskB->id,
        'user_id' => $userB->id,
    ]);
});

dataset('invalid task data', [
    'title exceeds 255 characters' => [
        ['title' => str_repeat('a', 256)],
        ['title'],
    ],

    'status is invalid' => [
        [
            'title' => 'Valid Title',
            'status' => 'invalid_status',
        ],
        ['status'],
    ],

    'title is empty' => [
        ['title' => ''],
        ['title'],
    ],
]);

test('validates invalid task creation data', function (
    array $payload,
    array $errors
) {
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->postJson('/api/tasks', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($errors);
})->with('invalid task data');
