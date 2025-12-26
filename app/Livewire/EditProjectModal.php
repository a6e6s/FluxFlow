<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\Priority;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EditProjectModal extends Component
{
    public bool $open = false;
    public ?int $projectId = null;

    #[Validate('required|string|min:2|max:100')]
    public string $title = '';

    public string $icon = '';
    public string $color = '#3b82f6';
    public string $priority = 'medium';

    public array $iconOptions = ['📁', '🚀', '💼', '🎯', '📊', '🔧', '💡', '🎨', '📱', '🌐', '⭐', '🔥'];
    public array $colorOptions = ['#3b82f6', '#8b5cf6', '#ec4899', '#ef4444', '#f97316', '#eab308', '#22c55e', '#14b8a6', '#06b6d4', '#6366f1'];

    // ─────────────────────────────────────────────────────────────
    // Actions
    // ─────────────────────────────────────────────────────────────

    #[On('open-edit-project-modal')]
    public function openModal(int $projectId): void
    {
        $project = Project::where('user_id', Auth::id())->findOrFail($projectId);

        $this->projectId = $project->id;
        $this->title = $project->title;
        $this->icon = $project->icon ?? '';
        $this->color = $project->color ?? '#3b82f6';
        $this->priority = $project->priority->value;

        $this->open = true;
    }

    public function update(): void
    {
        $this->validate();

        $project = Project::where('user_id', Auth::id())->findOrFail($this->projectId);

        $project->update([
            'title' => $this->title,
            'icon' => $this->icon ?: null,
            'color' => $this->color,
            'priority' => $this->priority,
        ]);

        $this->close();

        // Refresh sidebar
        $this->dispatch('project-updated');
    }

    public function delete(): void
    {
        $project = Project::where('user_id', Auth::id())->findOrFail($this->projectId);
        $project->delete();

        $this->close();

        // Refresh sidebar and clear selection
        $this->dispatch('project-deleted');
        $this->dispatch('project-selected', projectId: null);
    }

    public function close(): void
    {
        $this->open = false;
        $this->projectId = null;
    }

    // ─────────────────────────────────────────────────────────────
    // Render
    // ─────────────────────────────────────────────────────────────

    public function render(): View
    {
        return view('livewire.edit-project-modal');
    }
}
