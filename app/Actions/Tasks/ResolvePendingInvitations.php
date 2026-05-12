<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\TaskInvitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ResolvePendingInvitations
{
    public function handle(User $user): int
    {
        $email = mb_strtolower((string) $user->email);

        $invitations = TaskInvitation::query()
            ->whereNull('accepted_at')
            ->where('email', $email)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->get();

        if ($invitations->isEmpty()) {
            return 0;
        }

        DB::transaction(function () use ($invitations, $user): void {
            foreach ($invitations as $invitation) {
                $invitation->task?->collaborators()->syncWithoutDetaching([$user->id]);
                $invitation->forceFill(['accepted_at' => now()])->save();
            }
        });

        return $invitations->count();
    }
}
