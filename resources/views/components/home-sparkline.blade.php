@props(['values', 'color'])

@php
    $count = count($values);
    $max = max([1, ...$values]);
    $points = collect($values)
        ->map(fn (int $value, int $index): string => sprintf(
            '%.1f,%.1f',
            $count > 1 ? $index * 120 / ($count - 1) : 60,
            30 - ($value / $max) * 26,
        ))
        ->implode(' ');
@endphp

@if ($count > 0)
    <svg viewBox="0 0 120 32" preserveAspectRatio="none" class="h-8 w-full overflow-visible" aria-hidden="true">
        <polygon points="0,32 {{ $points }} 120,32" fill="{{ $color }}" fill-opacity="0.12" />
        <polyline points="{{ $points }}" fill="none" stroke="{{ $color }}" stroke-width="1.5" stroke-linejoin="round" vector-effect="non-scaling-stroke" />
    </svg>
@endif
