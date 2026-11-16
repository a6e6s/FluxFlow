@props(['task'])

<div class="flex items-center justify-between gap-3 border-b border-slate-100 py-2.5 last:border-0 dark:border-[#283239]">
    <div class="flex min-w-0 items-center gap-3">
        <span class="size-2 shrink-0 rounded-full" style="background-color: {{ $task->project?->color ?? '#64748b' }}"></span>
        <div class="min-w-0">
            <a href="{{ route('projects.show', $task->project_id) }}" wire:navigate
                class="block truncate text-sm font-medium text-slate-900 hover:text-[#1392ec] dark:text-white">{{ $task->title }}</a>
            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $task->project?->title }}</p>
        </div>
    </div>
    <div class="flex shrink-0 items-center gap-2">
        {{ $slot }}
    </div>
</div>
