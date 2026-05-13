<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\Tasks\InviteUserToTask;
use App\Enums\Priority;
use App\Models\Attachment;
use App\Models\Task;
use App\Models\TaskInvitation;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class TaskDetails extends Component
{
    use WithFileUploads;

    public bool $open = false;

    public ?int $taskId = null;

    // Form fields
    public string $title = '';

    public string $description = '';

    public ?string $dueDate = null;

    public ?int $effortScore = null;

    public Priority $priority = Priority::Medium;

    // Invitation form
    public string $inviteEmail = '';

    // File uploads
    #[Validate(['files.*' => 'file|max:10240'])] // 10MB max per file
    public array $files = [];

    public bool $uploading = false;

    // ─────────────────────────────────────────────────────────────
    // Computed Properties
    // ─────────────────────────────────────────────────────────────

    #[Computed]
    public function task(): ?Task
    {
        if (! $this->taskId) {
            return null;
        }

        $task = Task::query()
            ->with(['attachments', 'project'])
            ->find($this->taskId);

        if (! $task) {
            return null;
        }

        return Auth::user()->can('view', $task) ? $task : null;
    }

    #[Computed]
    public function attachments(): Collection
    {
        return $this->task?->attachments ?? collect();
    }

    #[Computed]
    public function isOwner(): bool
    {
        return $this->task !== null && $this->task->project?->user_id === Auth::id();
    }

    #[Computed]
    public function canEditDescription(): bool
    {
        return $this->task !== null && Auth::user()->can('update', $this->task);
    }

    #[Computed]
    public function collaborators(): Collection
    {
        if (! $this->task) {
            return collect();
        }

        return $this->task->collaborators()
            ->select(['users.id', 'users.name', 'users.email', 'users.profile_photo_path'])
            ->get();
    }

    #[Computed]
    public function pendingInvitations(): Collection
    {
        if (! $this->task) {
            return collect();
        }

        $collaboratorEmails = $this->collaborators->pluck('email')->map(fn ($e) => mb_strtolower((string) $e));

        return $this->task->pendingInvitations()
            ->whereNotIn('email', $collaboratorEmails)
            ->get();
    }

    /**
     * Live email preview: shows existing user info if found,
     * otherwise indicates a new invitation will be created.
     *
     * @return array{state: string, user?: User|null}
     */
    #[Computed]
    public function invitePreview(): array
    {
        $email = mb_strtolower(trim($this->inviteEmail));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['state' => 'idle'];
        }

        $user = User::query()
            ->select(['id', 'name', 'email', 'profile_photo_path'])
            ->where('email', $email)
            ->first();

        if (! $user) {
            return ['state' => 'new'];
        }

        if ($this->task && $user->id === $this->task->project?->user_id) {
            return ['state' => 'owner', 'user' => $user];
        }

        if ($this->task && $this->task->collaborators()->where('users.id', $user->id)->exists()) {
            return ['state' => 'already', 'user' => $user];
        }

        return ['state' => 'existing', 'user' => $user];
    }

    // ─────────────────────────────────────────────────────────────
    // Actions
    // ─────────────────────────────────────────────────────────────

    #[On('open-task-details')]
    public function openTask(int $taskId): void
    {
        $this->taskId = $taskId;
        $this->loadTask();
        $this->open = true;
    }

    public function loadTask(): void
    {
        if (! $this->task) {
            return;
        }

        $this->title = $this->task->title;
        $this->description = $this->task->description ?? '';
        $this->dueDate = $this->task->due_date?->format('Y-m-d');
        $this->priority = $this->task->priority;
        $this->effortScore = $this->task->effort_score;
        $this->inviteEmail = '';
        $this->files = [];

        $this->clearTaskCache();
    }

    public function setPriority(string $value): void
    {
        if (! $this->isOwner) {
            return;
        }

        $this->priority = Priority::from($value);
    }

    public function save(): void
    {
        if (! $this->task) {
            return;
        }

        $this->authorize('update', $this->task);

        if ($this->isOwner) {
            $this->task->update([
                'title' => $this->title,
                'description' => $this->description ?: null,
                'due_date' => $this->dueDate ?: null,
                'priority' => $this->priority,
                'effort_score' => $this->effortScore,
            ]);
        } else {
            $this->task->update([
                'description' => $this->description ?: null,
            ]);
        }

        $this->dispatch('task-updated');
        $this->dispatch('notify', message: __('app.save_changes'), type: 'success');
    }

    public function deleteTask(): void
    {
        if (! $this->task) {
            return;
        }

        $task = $this->task->loadMissing('attachments');

        $this->authorize('delete', $task);

        foreach ($task->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }

        $task->delete();

        $this->dispatch('task-deleted');
        $this->dispatch('task-updated');
        $this->dispatch('notify', message: __('app.task_deleted'), type: 'success');

        $this->close();
    }

    public function invite(InviteUserToTask $action): void
    {
        if (! $this->task) {
            return;
        }

        $this->authorize('invite', $this->task);

        $this->validate([
            'inviteEmail' => ['required', 'email:rfc'],
        ], [
            'inviteEmail.email' => __('invitations.invalid_email'),
            'inviteEmail.required' => __('invitations.invalid_email'),
        ]);

        $result = $action->handle($this->task, $this->inviteEmail, Auth::user());

        $message = match ($result['status']) {
            'attached' => __('invitations.attached', ['name' => $result['user']?->name ?? '']),
            'invited' => __('invitations.invited', ['email' => $result['invitation']?->email ?? '']),
            'already' => __('invitations.already_collaborator'),
            'owner' => __('invitations.invited_owner'),
            default => '',
        };

        $type = in_array($result['status'], ['already', 'owner'], true) ? 'error' : 'success';

        $this->inviteEmail = '';
        $this->clearTaskCache();
        $this->dispatch('notify', message: $message, type: $type);
        $this->dispatch('task-updated');
    }

    public function removeCollaborator(int $userId): void
    {
        if (! $this->task) {
            return;
        }

        $this->authorize('invite', $this->task);

        $this->task->collaborators()->detach($userId);

        $user = User::find($userId);
        if ($user) {
            $this->task->invitations()
                ->where('email', $user->email)
                ->delete();
        }

        $this->clearTaskCache();
        $this->dispatch('task-updated');
        $this->dispatch('notify', message: __('invitations.collaborator_removed'), type: 'success');
    }

    public function cancelInvitation(int $invitationId): void
    {
        if (! $this->task) {
            return;
        }

        $this->authorize('invite', $this->task);

        TaskInvitation::where('id', $invitationId)
            ->where('task_id', $this->task->id)
            ->delete();

        $this->clearTaskCache();
        $this->dispatch('notify', message: __('invitations.invitation_cancelled'), type: 'success');
    }

    public function uploadFiles(): void
    {
        $this->validate();

        if (empty($this->files) || ! $this->task) {
            return;
        }

        $this->authorize('update', $this->task);

        $this->uploading = true;

        foreach ($this->files as $file) {
            $path = $file->store('attachments/'.$this->task->project_id, 'public');

            $this->task->attachments()->create([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ]);
        }

        $this->files = [];
        $this->uploading = false;

        $this->clearTaskCache();
        $this->dispatch('task-updated');
    }

    public function removeFile(int $attachmentId): void
    {
        if (! $this->task) {
            return;
        }

        $this->authorize('update', $this->task);

        $attachment = Attachment::query()
            ->where('id', $attachmentId)
            ->whereHasMorph('attachable', Task::class, fn ($q) => $q->where('id', $this->task->id))
            ->first();

        if ($attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();

            $this->clearTaskCache();
            $this->dispatch('task-updated');
        }
    }

    public function removeTempFile(int $index): void
    {
        array_splice($this->files, $index, 1);
    }

    public function close(): void
    {
        $this->open = false;
        $this->taskId = null;
        $this->files = [];
        $this->inviteEmail = '';
        $this->reset(['title', 'description', 'dueDate', 'effortScore', 'priority']);
    }

    protected function clearTaskCache(): void
    {
        unset(
            $this->task,
            $this->attachments,
            $this->collaborators,
            $this->pendingInvitations,
            $this->invitePreview,
            $this->isOwner,
            $this->canEditDescription,
        );
    }

    // ─────────────────────────────────────────────────────────────
    // Render
    // ─────────────────────────────────────────────────────────────

    public function render(): View
    {
        return view('livewire.task-details');
    }
}
