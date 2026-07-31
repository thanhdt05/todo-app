<?php

namespace App\Jobs;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Notifications\TaskDueSoonNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendTaskReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 300, 900];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly int $taskId
    ) {
        $this->onQueue('notifications');
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping("task-reminder:{$this->taskId}"))
                ->expireAfter(300),
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $task = Task::query()->with('user')->find($this->taskId);

        if (! $task || ! $task->user) {
            return;
        }

        if ($task->status === TaskStatus::DONE) {
            return;
        }

        if ($task->due_date === null || $task->due_date->isPast()) {
            return;
        }

        if ($task->due_date->isAfter(now()->addDay())) {
            return;
        }

        if ($task->reminder_sent_at !== null) {
            return;
        }

        $task->user->notify(
            new TaskDueSoonNotification($task)
        );

        $task->forceFill([
            'reminder_sent_at' => now(),
        ])->save();
    }

    public function failed(?Throwable $exception)
    {
        logger()->error('Send task reminder failed',
            [
                'task_id' => $this->taskId,
                'exception' => $exception,
            ]
        );
    }
}
