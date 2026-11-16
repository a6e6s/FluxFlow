@props(['icon', 'title'])

<div class="flex flex-1 flex-col items-center justify-center gap-2 px-4 py-8 text-center">
    <span class="flex size-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-[#283239] dark:text-slate-400">
        <x-dynamic-component :component="'lucide-'.$icon" class="size-5" />
    </span>
    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $title }}</p>
    <p class="max-w-xs text-xs text-slate-500 dark:text-slate-400">{{ $slot }}</p>

    {{ $action ?? '' }}
</div>
