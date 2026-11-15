<?php

namespace App\Livewire\Home;

use App\Actions\Home\HomeInsights;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UpcomingDeadlines extends Component
{
    public function render(HomeInsights $insights): View
    {
        return view('livewire.home.upcoming-deadlines', ['tasks' => $insights->upcomingDeadlines(auth()->user())]);
    }
}
