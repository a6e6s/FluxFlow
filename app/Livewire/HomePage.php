<?php

namespace App\Livewire;

use App\Actions\Home\ResolvePeriod;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.kanban')]
class HomePage extends Component
{
    #[Url]
    public string $period = 'this_month';

    #[Url]
    public ?string $from = null;

    #[Url]
    public ?string $to = null;

    /**
     * The date range used by period-based widgets.
     *
     * @return array{start: string, end: string, label: string}
     */
    #[Computed]
    public function range(): array
    {
        return app(ResolvePeriod::class)->handle($this->period, $this->from, $this->to);
    }

    public function render(): View
    {
        return view('livewire.home-page');
    }
}
