<?php

namespace App\Livewire\Settings;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Appearance extends Component
{
    public function render(): View
    {
        return view('livewire.settings.appearance')
            ->layout('components.layouts.app', ['title' => __('Appearance')]);
    }
}
