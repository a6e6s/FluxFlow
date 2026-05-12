<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Tasks\ResolvePendingInvitations;
use App\Models\User;

class ResolveInvitationsOnAuth
{
    public function __construct(private ResolvePendingInvitations $action) {}

    public function handle(object $event): void
    {
        $user = $event->user ?? null;

        if ($user instanceof User) {
            $this->action->handle($user);
        }
    }
}
