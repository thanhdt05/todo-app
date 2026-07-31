<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDueSoonNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Task $task
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Công việc sắp đến hạn')
            ->greeting("Xin chào {$notifiable->name},")
            ->line("Công việc \"{$this->task->title}\" sắp đến hạn.")
            ->line(
                'Hạn hoàn thành: '
                .$this->task->due_date?->format('d/m/Y H:i')
            )
            ->action(
                'Mở danh sách công việc',
                rtrim(config('app.frontend_url'), '/').'/tasks'
            )
            ->line('Vui lòng kiểm tra và hoàn thành công việc đúng hạn.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_due_soon',
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'due_date' => $this->task->due_date?->toISOString(),
        ];
    }
}
