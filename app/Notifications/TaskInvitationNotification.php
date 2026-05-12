<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Task;
use App\Models\TaskInvitation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public User $inviter,
        public ?TaskInvitation $invitation = null,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'task_invitation',
            'invitation_id' => $this->invitation?->id,
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'project_id' => $this->task->project_id,
            'project_title' => $this->task->project?->title,
            'inviter_id' => $this->inviter->id,
            'inviter_name' => $this->inviter->name,
            'message' => __('invitations.notification.body', [
                'inviter' => $this->inviter->name,
                'task' => $this->task->title,
            ]),
        ];
    }
}
