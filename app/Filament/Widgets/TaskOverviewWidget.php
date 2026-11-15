<?php

namespace App\Filament\Widgets;

use App\Enums\TaskStatus;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TaskOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $countByStatus = Task::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            Stat::make(__('admin.widgets.total_tasks'), Task::query()->count())
                ->icon('heroicon-o-clipboard-document-list'),
        ];

        foreach (TaskStatus::cases() as $status) {
            $stats[] = Stat::make($status->label(), (int) ($countByStatus[$status->value] ?? 0))
                ->icon(match ($status) {
                    TaskStatus::Todo => 'heroicon-o-circle-stack',
                    TaskStatus::Doing => 'heroicon-o-arrow-path',
                    TaskStatus::Review => 'heroicon-o-eye',
                    TaskStatus::Done => 'heroicon-o-check-circle',
                });
        }

        $stats[] = Stat::make(__('admin.status.overdue'), Task::query()->overdue()->count())
            ->icon('heroicon-o-exclamation-triangle')
            ->color('danger');

        return $stats;
    }
}
