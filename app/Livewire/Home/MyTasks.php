<?php

namespace App\Livewire\Home;

use App\Actions\Home\HomeInsights;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class MyTasks extends Component
{
    public function render(HomeInsights $insights): View
    {
        return view('livewire.home.my-tasks', ['tasks' => $insights->myTasks(auth()->user())]);
    }
}
