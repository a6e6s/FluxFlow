<x-home-card :title="__('home.widgets.recent_activity')" icon="history" tone="violet">
    @php
        $groups = $tasks->groupBy(fn ($task) => match (true) {
            $task->updated_at->isToday() => 'today',
            $task->updated_at->isYesterday() => 'yesterday',
            default => 'earlier',
        });
    @endphp

    @forelse ($groups as $group => $items)
        <p class="mb-1 mt-4 text-[10px] font-semibold uppercase tracking-wider text-slate-400 first:mt-0 dark:text-slate-500">{{ __('home.activity.'.$group) }}</p>

        @foreach ($items as $task)
            <div class="flex items-start gap-3 border-b border-slate-100 py-2.5 last:border-0 dark:border-[#283239]">
                <span class="mt-1.5 size-2 shrink-0 rounded-full" style="background-color: {{ $task->project?->color ?? '#64748b' }}"></span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-slate-900 dark:text-white">{{ $task->title }}</p>
                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $task->project?->title }} · {{ $task->updated_at->diffForHumans() }}</p>
                </div>
                <span class="shrink-0 rounded px-1.5 py-0.5 text-[11px] font-semibold {{ $task->status->bgColor() }} {{ $task->status->color() }}">{{ $task->status->label() }}</span>
            </div>
        @endforeach
    @empty
        <x-home-empty icon="history" :title="__('home.widgets.recent_activity')">
            {{ __('home.activity.empty') }}
        </x-home-empty>
    @endforelse
</x-home-card>
