<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Jobs\SendTaskReminder;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskDueSoonNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TaskReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_dispatches_job_for_task_due_soon(): void
    {
        Queue::fake();

        $user = User::factory()->user()->create();
        $task = Task::factory()->for($user)->create([
            'status' => TaskStatus::TODO,
            'due_date' => now()->addHours(12),
            'reminder_sent_at' => null,
        ]);

        $this->artisan('tasks:dispatch-reminders')
            ->assertSuccessful();

        Queue::assertPushed(
            SendTaskReminder::class,
            fn (SendTaskReminder $job): bool => $job->taskId === $task->id
        );
    }

    public function test_command_ignores_completed_task(): void
    {
        Queue::fake();

        $user = User::factory()->user()->create();
        Task::factory()->for($user)->create([
            'status' => TaskStatus::DONE,
            'due_date' => now()->addHours(12),
            'reminder_sent_at' => null,
        ]);

        $this->artisan('tasks:dispatch-reminders')
            ->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_job_sends_notification_and_mail_to_user(): void
    {
        Notification::fake();

        $user = User::factory()->user()->create();
        $task = Task::factory()->for($user)->create([
            'status' => TaskStatus::TODO,
            'due_date' => now()->addHours(12),
            'reminder_sent_at' => null,
        ]);

        // Dispatch job xử lý gửi reminder
        (new SendTaskReminder($task->id))->handle();

        // Xác nhận Notification được gửi đúng tới user và đúng kênh (mail, database)
        Notification::assertSentTo(
            $user,
            TaskDueSoonNotification::class,
            function (TaskDueSoonNotification $notification, array $channels) use ($task): bool {
                return $notification->task->id === $task->id
                    && in_array('mail', $channels, true)
                    && in_array('database', $channels, true);
            }
        );
    }
}
