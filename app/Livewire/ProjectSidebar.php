<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Component;

class ProjectSidebar extends Component
{
    public ?int $selectedProjectId = null;

    #[Session]
    public bool $collapsed = false;

    // ─────────────────────────────────────────────────────────────
    // Computed Properties
    // ─────────────────────────────────────────────────────────────

    #[Computed(persist: true)]
    public function ownedProjects(): Collection
    {
        return Project::query()
            ->select(['id', 'user_id', 'title', 'icon', 'color', 'sort_order', 'priority', 'archived_at'])
            ->where('user_id', Auth::id())
            ->active()
            ->ordered()
            ->withCount(['tasks', 'tasks as done_tasks_count' => function ($query) {
                $query->where('status', 'done');
            }])
            ->get();
    }

    #[Computed(persist: true)]
    public function sharedProjects(): Collection
    {
        return Project::query()
            ->select(['id', 'user_id', 'title', 'icon', 'color', 'sort_order', 'priority', 'archived_at'])
            ->visibleTo(Auth::user())
            ->where('user_id', '!=', Auth::id())
            ->active()
            ->ordered()
            ->withCount(['tasks', 'tasks as done_tasks_count' => function ($query) {
                $query->where('status', 'done');
            }])
            ->get();
    }

    #[Computed(persist: true)]
    public function archivedProjects(): Collection
    {
        return Project::query()
            ->select(['id', 'user_id', 'title', 'icon', 'color', 'sort_order', 'priority', 'archived_at'])
            ->where('user_id', Auth::id())
            ->archived()
            ->ordered()
            ->withCount(['tasks', 'tasks as done_tasks_count' => function ($query) {
                $query->where('status', 'done');
            }])
            ->get();
    }

    // ─────────────────────────────────────────────────────────────
    // Actions
    // ─────────────────────────────────────────────────────────────

    public function selectProject(int $projectId): void
    {
        $this->selectedProjectId = $projectId;
        $this->dispatch('project-selected', projectId: $projectId);
    }

    public function toggleSidebar(): void
    {
        $this->collapsed = ! $this->collapsed;
    }

    public function reorderProjects(array $ids): void
    {
        foreach ($ids as $index => $id) {
            Project::query()
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->update(['sort_order' => $index]);
        }

        unset($this->ownedProjects);
    }

    public function archiveProject(int $projectId): void
    {
        Project::query()
            ->where('id', $projectId)
            ->where('user_id', Auth::id())
            ->update(['archived_at' => now()]);

        unset($this->ownedProjects, $this->sharedProjects, $this->archivedProjects);

        if ($this->selectedProjectId === $projectId) {
            $this->selectedProjectId = null;
        }
    }

    public function restoreProject(int $projectId): void
    {
        Project::query()
            ->where('id', $projectId)
            ->where('user_id', Auth::id())
            ->update(['archived_at' => null]);

        unset($this->ownedProjects, $this->sharedProjects, $this->archivedProjects);
    }

    #[On('task-moved')]
    #[On('project-archived')]
    public function refreshProjects(): void
    {
        unset($this->ownedProjects, $this->sharedProjects, $this->archivedProjects);
        $this->selectedProjectId = null;
    }

    #[On('task-created')]
    #[On('task-deleted')]
    public function refreshProjectCounts(): void
    {
        unset($this->ownedProjects, $this->sharedProjects, $this->archivedProjects);
    }

    #[On('project-created')]
    public function onProjectCreated(): void
    {
        unset($this->ownedProjects, $this->sharedProjects);
    }

    #[On('project-updated')]
    #[On('project-deleted')]
    public function onProjectUpdated(): void
    {
        unset($this->ownedProjects, $this->sharedProjects, $this->archivedProjects);
    }

    // ─────────────────────────────────────────────────────────────
    // Render
    // ─────────────────────────────────────────────────────────────

    public function render(): View
    {
        return view('livewire.project-sidebar');
    }
}
