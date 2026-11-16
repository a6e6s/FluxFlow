@props(['title', 'icon', 'tone' => 'primary'])

@php
    $iconTone = match ($tone) {
        'amber' => 'bg-amber-400/15 text-amber-500 dark:text-amber-400',
        'rose' => 'bg-rose-400/15 text-rose-500 dark:text-rose-400',
        'emerald' => 'bg-emerald-400/15 text-emerald-600 dark:text-emerald-400',
        'violet' => 'bg-violet-400/15 text-violet-500 dark:text-violet-400',
        default => 'bg-[#1392ec]/15 text-[#1392ec]',
    };
@endphp

<section class="flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#283239] dark:bg-[#1c2630]">
    <header class="mb-4 flex items-center justify-between gap-3">
        <div class="flex min-w-0 items-center gap-2.5">
            <span class="flex size-8 shrink-0 items-center justify-center rounded-lg {{ $iconTone }}">
                <x-dynamic-component :component="'lucide-'.$icon" class="size-4" />
            </span>
            <h2 class="truncate text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-200">{{ $title }}</h2>
            {{ $badge ?? '' }}
        </div>

        {{ $actions ?? '' }}
    </header>

    {{ $slot }}
</section>
