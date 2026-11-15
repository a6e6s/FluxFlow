<?php

namespace App\Livewire\Home;

use App\Actions\Home\HomeInsights;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class DueSoon extends Component
{
    public function render(HomeInsights $insights): View
    {
        return view('livewire.home.due-soon', ['tasks' => $insights->dueSoon(auth()->user())]);
    }
}
