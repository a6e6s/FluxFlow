<?php

namespace App\Livewire\Home;

use App\Actions\Home\HomeInsights;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SummaryStats extends Component
{
    public string $from;

    public string $to;

    public function render(): View
    {
        return view('livewire.home.summary-stats', ['stats' => $this->metrics()]);
    }

    /**
     * Counts visible tasks due within the period, plus all active projects.
     *
     * @return list<array{label: string, value: int, color: string}>
     */
    public function metrics(): array
    {
        $summary = app(HomeInsights::class)->summary(auth()->user(), $this->from, $this->to);

        return [
            ['label' => __('home.stats.open'), 'value' => $summary['open'], 'color' => 'text-slate-900 dark:text-white'],
            ['label' => __('home.stats.in_progress'), 'value' => $summary['doing'], 'color' => 'text-[#1392ec]'],
            ['label' => __('home.stats.in_review'), 'value' => $summary['review'], 'color' => 'text-amber-400'],
            ['label' => __('home.stats.done'), 'value' => $summary['done'], 'color' => 'text-emerald-400'],
            ['label' => __('home.stats.active_projects'), 'value' => $summary['active_projects'], 'color' => 'text-slate-900 dark:text-white'],
        ];
    }
}
