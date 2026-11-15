<?php

namespace App\Livewire\Home;

use App\Actions\Home\HomeInsights;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PendingInvitations extends Component
{
    public function render(HomeInsights $insights): View
    {
        return view('livewire.home.pending-invitations', ['invitations' => $insights->pendingInvitations(auth()->user())]);
    }
}
