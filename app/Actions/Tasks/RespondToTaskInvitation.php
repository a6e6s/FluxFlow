<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\TaskInvitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RespondToTaskInvitation
{
    /**
     * Accept the invitation: attach the user as a collaborator and mark accepted.
     */
    public function accept(TaskInvitation $invitation, User $user): bool
    {
        if (! $this->belongsTo($invitation, $user)) {
            return false;
        }

        if (! $invitation->isPending()) {
            return false;
        }

        DB::transaction(function () use ($invitation, $user): void {
            $invitation->task?->collaborators()->syncWithoutDetaching([$user->id]);
            $invitation->forceFill([
                'accepted_at' => now(),
                'declined_at' => null,
            ])->save();
        });

        return true;
    }

    /**
     * Decline the invitation: mark declined.
     */
    public function decline(TaskInvitation $invitation, User $user): bool
    {
        if (! $this->belongsTo($invitation, $user)) {
            return false;
        }

        if (! $invitation->isPending()) {
            return false;
        }

        $invitation->forceFill([
            'declined_at' => now(),
            'accepted_at' => null,
        ])->save();

        return true;
    }

    private function belongsTo(TaskInvitation $invitation, User $user): bool
    {
        return mb_strtolower((string) $user->email) === mb_strtolower((string) $invitation->email);
    }
}
