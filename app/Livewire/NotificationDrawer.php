<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\Tasks\RespondToTaskInvitation;
use App\Models\TaskInvitation;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationDrawer extends Component
{
    public bool $open = false;

    public function toggle(): void
    {
        $this->open = ! $this->open;

        if ($this->open) {
            unset($this->pendingInvitations);
        }
    }

    public function close(): void
    {
        $this->open = false;
    }

    #[On('refresh-notifications')]
    public function refresh(): void
    {
        unset($this->pendingInvitations);
    }

    /**
     * @return Collection<int, TaskInvitation>
     */
    #[Computed]
    public function pendingInvitations(): Collection
    {
        return TaskInvitation::query()
            ->with(['task.project', 'inviter'])
            ->pendingFor((string) $this->currentUser()->email)
            ->latest()
            ->get();
    }

    #[Computed]
    public function unreadCount(): int
    {
        return $this->pendingInvitations->count();
    }

    public function accept(int $invitationId, RespondToTaskInvitation $action): void
    {
        $invitation = TaskInvitation::find($invitationId);

        if (! $invitation) {
            return;
        }

        if ($action->accept($invitation, $this->currentUser())) {
            unset($this->pendingInvitations);
            $this->dispatch('notify', message: __('invitations.accepted'), type: 'success');
            $this->dispatch('task-updated');
            $this->dispatch('project-created');
        }
    }

    public function decline(int $invitationId, RespondToTaskInvitation $action): void
    {
        $invitation = TaskInvitation::find($invitationId);

        if (! $invitation) {
            return;
        }

        if ($action->decline($invitation, $this->currentUser())) {
            unset($this->pendingInvitations);
            $this->dispatch('notify', message: __('invitations.declined'), type: 'success');
        }
    }

    public function render(): View
    {
        return view('livewire.notification-drawer');
    }

    private function currentUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
