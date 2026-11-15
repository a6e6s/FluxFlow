<?php

namespace App\Livewire\Home;

use App\Actions\Home\HomeInsights;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class RecentActivity extends Component
{
    public function render(HomeInsights $insights): View
    {
        return view('livewire.home.recent-activity', ['tasks' => $insights->recentActivity(auth()->user())]);
    }
}
