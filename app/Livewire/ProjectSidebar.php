<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ProjectSidebar extends Component
{
    public ?int $selectedProjectId = null;

    // ─────────────────────────────────────────────────────────────
    // Computed Properties
    // ─────────────────────────────────────────────────────────────

    #[Computed]
    public function projects(): Collection
    {
        return Project::query()
            ->where('user_id', Auth::id())
            ->active()
            ->ordered()
            ->withCount(['tasks', 'tasks as done_tasks_count' => function ($query) {
                $query->where('status', 'done');
            }])
            ->get();
    }

    #[Computed]
    public function archivedProjects(): Collection
    {
        return Project::query()
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

    public function reorderProjects(array $ids): void
    {
        foreach ($ids as $index => $id) {
            Project::query()
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->update(['sort_order' => $index]);
        }

        unset($this->projects);
    }

    public function archiveProject(int $projectId): void
    {
        Project::query()
            ->where('id', $projectId)
            ->where('user_id', Auth::id())
            ->update(['archived_at' => now()]);

        unset($this->projects, $this->archivedProjects);

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

        unset($this->projects, $this->archivedProjects);
    }

    // ─────────────────────────────────────────────────────────────
    // Render
    // ─────────────────────────────────────────────────────────────

    public function render(): View
    {
        return view('livewire.project-sidebar');
    }
}
