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
     * Counts visible tasks due within the period, plus all active projects, with per-day series for sparklines.
     *
     * @return list<array{label: string, value: int, icon: string, pill: string, tone: string, accent: string, series: list<int>}>
     */
    public function metrics(): array
    {
        $user = auth()->user();
        $insights = app(HomeInsights::class);
        $summary = $insights->summary($user, $this->from, $this->to);
        $series = $insights->dailySeries($user, $this->from, $this->to);

        return [
            [
                'label' => __('home.stats.open'),
                'value' => $summary['open'],
                'icon' => 'list-checks',
                'pill' => $summary['overdue'] > 0 ? __('home.pills.overdue_count', ['count' => $summary['overdue']]) : __('home.pills.none_overdue'),
                'tone' => $summary['overdue'] > 0 ? 'rose' : 'emerald',
                'accent' => '#1392ec',
                'series' => $series['open'],
            ],
            [
                'label' => __('home.stats.in_progress'),
                'value' => $summary['doing'],
                'icon' => 'loader',
                'pill' => $summary['doing'] > 0 ? __('home.pills.active') : __('home.pills.idle'),
                'tone' => $summary['doing'] > 0 ? 'primary' : 'slate',
                'accent' => '#f97316',
                'series' => $series['doing'],
            ],
            [
                'label' => __('home.stats.in_review'),
                'value' => $summary['review'],
                'icon' => 'eye',
                'pill' => $summary['review'] > 0 ? __('home.pills.awaiting_review') : __('home.pills.none'),
                'tone' => $summary['review'] > 0 ? 'amber' : 'slate',
                'accent' => '#f59e0b',
                'series' => $series['review'],
            ],
            [
                'label' => __('home.stats.done'),
                'value' => $summary['done'],
                'icon' => 'circle-check',
                'pill' => $summary['done'] > 0 ? __('home.pills.completed_count', ['count' => $summary['done']]) : __('home.pills.none_yet'),
                'tone' => $summary['done'] > 0 ? 'emerald' : 'slate',
                'accent' => '#10b981',
                'series' => $series['done'],
            ],
            [
                'label' => __('home.stats.active_projects'),
                'value' => $summary['active_projects'],
                'icon' => 'folder-kanban',
                'pill' => __('home.pills.all_time'),
                'tone' => 'slate',
                'accent' => '#8b5cf6',
                'series' => $series['active_projects'],
            ],
        ];
    }
}
