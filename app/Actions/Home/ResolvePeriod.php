<?php

declare(strict_types=1);

namespace App\Actions\Home;

use Carbon\Carbon;

/**
 * Resolves a home period selection into a date range. Invalid custom dates fall back to the current month.
 */
class ResolvePeriod
{
    /**
     * @return array{start: string, end: string, label: string}
     */
    public function handle(string $period, ?string $from = null, ?string $to = null): array
    {
        return match ($period) {
            'last_month' => $this->monthRange(today()->subMonthNoOverflow()),
            'custom' => $this->customRange($from, $to),
            default => $this->monthRange(today()),
        };
    }

    /**
     * @return array{start: string, end: string, label: string}
     */
    private function monthRange(Carbon $date): array
    {
        $start = $date->copy()->startOfMonth();

        return [
            'start' => $start->toDateString(),
            'end' => $date->copy()->endOfMonth()->toDateString(),
            'label' => $start->translatedFormat('F Y'),
        ];
    }

    /**
     * @return array{start: string, end: string, label: string}
     */
    private function customRange(?string $from, ?string $to): array
    {
        $start = $from ? Carbon::createFromFormat('Y-m-d', $from) : false;
        $end = $to ? Carbon::createFromFormat('Y-m-d', $to) : false;

        if (! $start || ! $end || $start->gt($end)) {
            return $this->monthRange(today());
        }

        return [
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'label' => $start->translatedFormat('j M Y').' – '.$end->translatedFormat('j M Y'),
        ];
    }
}
