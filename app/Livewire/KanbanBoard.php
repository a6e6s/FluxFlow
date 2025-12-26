<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.kanban')]
class KanbanBoard extends Component
{
    public ?int $projectId = null;

    protected $listeners = [
        'project-selected' => 'selectProject',
    ];

    public function selectProject(int $projectId): void
    {
        $this->projectId = $projectId;
    }

    public function render(): View
    {
        return view('livewire.kanban-board');
    }
}
