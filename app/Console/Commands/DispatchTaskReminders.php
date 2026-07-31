<?php

namespace App\Console\Commands;

use App\Enums\TaskStatus;
use App\Jobs\SendTaskReminder;
use App\Models\Task;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('tasks:dispatch-reminders')]
#[Description('Dispatch reminder jobs for tasks due within 24 hours')]
class DispatchTaskReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dispatched = 0;

        Task::query()
            ->whereNotNull('due_date')
            ->whereNull('reminder_sent_at')
            ->where('status', '!=', TaskStatus::DONE->value)
            ->whereBetween('due_date', [
                now(),
                now()->addDay(),
            ])
            ->orderBy('id')
            ->chunk(100, function ($tasks) use (&$dispatched): void {
                foreach ($tasks as $task) {
                    SendTaskReminder::dispatch($task->id);
                    $dispatched++;
                }
            });

        $this->info("Dispatched {$dispatched} reminder jobs.");

        return self::SUCCESS;
    }
}
