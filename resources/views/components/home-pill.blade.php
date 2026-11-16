@props(['tone' => 'slate'])

@php
    $toneClasses = match ($tone) {
        'primary' => 'bg-[#1392ec]/15 text-[#1392ec]',
        'amber' => 'bg-amber-400/15 text-amber-600 dark:text-amber-400',
        'rose' => 'bg-rose-400/15 text-rose-600 dark:text-rose-400',
        'emerald' => 'bg-emerald-400/15 text-emerald-600 dark:text-emerald-400',
        default => 'bg-slate-500/15 text-slate-600 dark:text-slate-300',
    };
@endphp

<span class="inline-flex items-center whitespace-nowrap rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $toneClasses }}">{{ $slot }}</span>
