<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskNotification extends Notification implements ShouldQueue // Implement ShouldQueue if you want to queue the notifications
{
    use Queueable;

    protected $task;

    public function __construct($task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];  // You can add other channels like 'database', 'sms', etc.
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Task Created: ' . $this->task->task_name)
            ->greeting('Hello!')
            ->line('A new task has been created: ' . $this->task->task_name)
            ->line('Description: ' . $this->task->task_description)
            ->action('View Task', url('/tasks/' . $this->task->id))
            ->line('Thank you for using our application!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'task_name' => $this->task->task_name,
            'task_description' => $this->task->task_description,
            'created_at' => now(),
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'task_name' => $this->task->task_name,
            'task_description' => $this->task->task_description,
        ];
    }
}
