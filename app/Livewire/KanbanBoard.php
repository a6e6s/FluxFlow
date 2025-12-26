<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.kanban')]
class KanbanBoard extends Component
{
    public ?int $projectId = null;

    // Cache all tasks in one query
    protected ?Collection $allTasks = null;

    // ─────────────────────────────────────────────────────────────
    // Lifecycle
    // ─────────────────────────────────────────────────────────────

    public function mount(?int $projectId = null): void
    {
        $this->projectId = $projectId;
    }

    // ─────────────────────────────────────────────────────────────
    // Computed Properties
    // ─────────────────────────────────────────────────────────────

    #[Computed]
    public function project(): ?Project
    {
        if (!$this->projectId) {
            return null;
        }

        return Project::query()
            ->select(['id', 'user_id', 'title', 'icon', 'color', 'priority', 'archived_at'])
            ->where('user_id', Auth::id())
            ->find($this->projectId);
    }

    /**
     * Load all tasks in ONE query, then filter in PHP
     */
    #[Computed]
    public function tasks(): Collection
    {
        if (!$this->projectId) {
            return collect();
        }

        return Task::query()
            ->select(['id', 'project_id', 'assigned_to', 'title', 'priority', 'status', 'sort_order', 'due_date', 'effort_score', 'updated_at'])
            ->where('project_id', $this->projectId)
            ->with(['assignee:id,name,profile_photo_path'])
            ->withCount('attachments')
            ->ordered()
            ->get();
    }

    #[Computed]
    public function todoTasks(): Collection
    {
        return $this->tasks->where('status', TaskStatus::Todo)->values();
    }

    #[Computed]
    public function doingTasks(): Collection
    {
        return $this->tasks->where('status', TaskStatus::Doing)->values();
    }

    #[Computed]
    public function reviewTasks(): Collection
    {
        return $this->tasks->where('status', TaskStatus::Review)->values();
    }

    #[Computed]
    public function doneTasks(): Collection
    {
        return $this->tasks->where('status', TaskStatus::Done)->values();
    }

    // ─────────────────────────────────────────────────────────────
    // Actions
    // ─────────────────────────────────────────────────────────────

    #[On('project-selected')]
    public function selectProject(?int $projectId): void
    {
        $this->projectId = $projectId;
        $this->clearTaskCache();
    }

    #[On('task-created')]
    public function onTaskCreated(): void
    {
        $this->clearTaskCache();
    }

    public function moveTask(int $taskId, string $newStatus, array $orderedIds): void
    {
        $status = TaskStatus::tryFrom($newStatus);

        if (!$status) {
            return;
        }

        // Use a single query with CASE WHEN for bulk update
        DB::transaction(function () use ($taskId, $status, $orderedIds) {
            // Update the moved task's status
            Task::query()
                ->where('id', $taskId)
                ->where('project_id', $this->projectId)
                ->update(['status' => $status]);

            // Bulk update sort order using CASE WHEN
            if (!empty($orderedIds)) {
                $cases = [];
                $ids = [];
                foreach ($orderedIds as $index => $id) {
                    $cases[] = "WHEN id = {$id} THEN {$index}";
                    $ids[] = $id;
                }

                if (!empty($cases)) {
                    $caseStatement = implode(' ', $cases);
                    DB::table('tasks')
                        ->whereIn('id', $ids)
                        ->where('project_id', $this->projectId)
                        ->update(['sort_order' => DB::raw("CASE {$caseStatement} END")]);
                }
            }
        });

        $this->clearTaskCache();

        // Dispatch event to update sidebar progress
        $this->dispatch('task-moved');
    }

    public function reorderTasks(string $status, array $orderedIds): void
    {
        if (empty($orderedIds)) {
            return;
        }

        // Bulk update using CASE WHEN
        $cases = [];
        $ids = [];
        foreach ($orderedIds as $index => $id) {
            $cases[] = "WHEN id = {$id} THEN {$index}";
            $ids[] = $id;
        }

        $caseStatement = implode(' ', $cases);
        DB::table('tasks')
            ->whereIn('id', $ids)
            ->where('project_id', $this->projectId)
            ->update(['sort_order' => DB::raw("CASE {$caseStatement} END")]);

        $this->clearTaskCache();
    }

    public function archiveProject(): void
    {
        if (!$this->projectId) {
            return;
        }

        Project::query()
            ->where('id', $this->projectId)
            ->where('user_id', Auth::id())
            ->update(['archived_at' => now()]);

        $this->clearTaskCache();

        // Notify sidebar to refresh
        $this->dispatch('project-archived');

        // Clear selection
        $this->projectId = null;
    }

    #[On('task-updated')]
    public function refreshTasks(): void
    {
        $this->clearTaskCache();
    }

    protected function clearTaskCache(): void
    {
        unset($this->tasks, $this->todoTasks, $this->doingTasks, $this->reviewTasks, $this->doneTasks, $this->project);
    }

    // ─────────────────────────────────────────────────────────────
    // Render
    // ─────────────────────────────────────────────────────────────

    public function render(): View
    {
        return view('livewire.kanban-board');
    }
}
