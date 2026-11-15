<x-home-card :title="__('home.widgets.due_soon')" icon="triangle-alert">
    @forelse ($tasks as $task)
        <x-home-task-row :task="$task">
            @if ($task->isOverdue())
                <span class="text-xs font-semibold text-rose-400">{{ __('home.labels.overdue') }}</span>
            @else
                <span class="text-xs font-semibold text-amber-400">{{ __('home.labels.today') }}</span>
            @endif
        </x-home-task-row>
    @empty
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('home.empty') }}</p>
    @endforelse
</x-home-card>
