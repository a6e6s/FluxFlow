<?php

namespace App\Livewire\Home;

use App\Actions\Home\HomeInsights;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ProjectProgress extends Component
{
    public function render(HomeInsights $insights): View
    {
        return view('livewire.home.project-progress', ['projects' => $insights->projectProgress(auth()->user())]);
    }
}
