<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Attachment;
use App\Models\Task;
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
    public ?int $assigneeId = null;
    public ?int $effortScore = null;

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
        if (!$this->taskId) {
            return null;
        }

        return Task::query()
            ->with(['attachments', 'assignee', 'project'])
            ->whereHas('project', fn ($q) => $q->where('user_id', Auth::id()))
            ->find($this->taskId);
    }

    #[Computed]
    public function attachments(): Collection
    {
        return $this->task?->attachments ?? collect();
    }

    #[Computed]
    public function teamMembers(): Collection
    {
        // For now, just get the current user. In a real app, you'd get team members
        return User::query()
            ->select(['id', 'name', 'profile_photo_path'])
            ->where('id', Auth::id())
            ->get();
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
        if (!$this->task) {
            return;
        }

        $this->title = $this->task->title;
        $this->description = $this->task->description ?? '';
        $this->dueDate = $this->task->due_date?->format('Y-m-d');
        $this->assigneeId = $this->task->assigned_to;
        $this->effortScore = $this->task->effort_score;

        // Clear file uploads
        $this->files = [];

        // Clear computed cache
        unset($this->attachments);
    }

    public function save(): void
    {
        if (!$this->task) {
            return;
        }

        $this->task->update([
            'title' => $this->title,
            'description' => $this->description ?: null,
            'due_date' => $this->dueDate ?: null,
            'assigned_to' => $this->assigneeId,
            'effort_score' => $this->effortScore,
        ]);

        $this->dispatch('task-updated');
        $this->dispatch('notify', message: 'Task updated successfully', type: 'success');
    }

    public function uploadFiles(): void
    {
        $this->validate();

        if (empty($this->files) || !$this->task) {
            return;
        }

        $this->uploading = true;

        foreach ($this->files as $file) {
            $path = $file->store('attachments/' . $this->task->project_id, 'public');

            $this->task->attachments()->create([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ]);
        }

        $this->files = [];
        $this->uploading = false;

        // Clear computed cache
        unset($this->attachments, $this->task);

        $this->dispatch('task-updated');
        $this->dispatch('notify', message: 'Files uploaded successfully', type: 'success');
    }

    public function removeFile(int $attachmentId): void
    {
        $attachment = Attachment::query()
            ->where('id', $attachmentId)
            ->whereHasMorph('attachable', Task::class, function ($query) {
                $query->whereHas('project', fn ($q) => $q->where('user_id', Auth::id()));
            })
            ->first();

        if ($attachment) {
            // Delete from storage
            Storage::disk('public')->delete($attachment->file_path);

            // Soft delete from database
            $attachment->delete();

            // Clear computed cache
            unset($this->attachments, $this->task);

            $this->dispatch('task-updated');
            $this->dispatch('notify', message: 'File removed', type: 'success');
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
        $this->reset(['title', 'description', 'dueDate', 'assigneeId', 'effortScore']);
    }

    // ─────────────────────────────────────────────────────────────
    // Render
    // ─────────────────────────────────────────────────────────────

    public function render(): View
    {
        return view('livewire.task-details');
    }
}
