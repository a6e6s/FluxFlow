<?php

namespace App\Livewire\Home;

use App\Actions\Home\HomeInsights;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EffortLoad extends Component
{
    public string $from;

    public string $to;

    public function render(HomeInsights $insights): View
    {
        $projects = $insights->effortLoad(auth()->user(), $this->from, $this->to);

        return view('livewire.home.effort-load', [
            'projects' => $projects,
            'maxEffort' => max(1, (int) $projects->max('effort')),
        ]);
    }
}
