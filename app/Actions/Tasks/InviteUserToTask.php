<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Mail\TaskInvitationMail;
use App\Models\Task;
use App\Models\TaskInvitation;
use App\Models\User;
use App\Notifications\TaskInvitationNotification;
use Illuminate\Support\Facades\Mail;

class InviteUserToTask
{
    /**
     * @return array{status: string, invitation: ?TaskInvitation, user: ?User}
     */
    public function handle(Task $task, string $email, User $inviter): array
    {
        $email = mb_strtolower(trim($email));

        $existing = User::where('email', $email)->first();

        if ($existing) {
            if ($existing->id === $task->project->user_id) {
                return ['status' => 'owner', 'invitation' => null, 'user' => $existing];
            }

            $alreadyCollaborator = $task->collaborators()->where('users.id', $existing->id)->exists();

            if ($alreadyCollaborator) {
                return ['status' => 'already', 'invitation' => null, 'user' => $existing];
            }

            $invitation = TaskInvitation::updateOrCreate(
                ['task_id' => $task->id, 'email' => $existing->email],
                [
                    'invited_by_id' => $inviter->id,
                    'token' => TaskInvitation::generateToken(),
                    'expires_at' => now()->addDays(14),
                    'accepted_at' => null,
                    'declined_at' => null,
                ],
            );

            $existing->notify(new TaskInvitationNotification($task, $inviter, $invitation));

            return ['status' => 'invited', 'invitation' => $invitation, 'user' => $existing];
        }

        $invitation = TaskInvitation::updateOrCreate(
            ['task_id' => $task->id, 'email' => $email],
            [
                'invited_by_id' => $inviter->id,
                'token' => TaskInvitation::generateToken(),
                'expires_at' => now()->addDays(14),
                'accepted_at' => null,
                'declined_at' => null,
            ],
        );

        Mail::to($email)->send(new TaskInvitationMail($invitation));

        return ['status' => 'invited', 'invitation' => $invitation, 'user' => null];
    }
}
