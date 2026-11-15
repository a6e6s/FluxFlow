<x-home-card :title="__('home.widgets.upcoming')" icon="calendar">
    @forelse ($tasks as $task)
        <x-home-task-row :task="$task">
            <span class="text-xs text-slate-500 dark:text-slate-400">{{ $task->due_date->translatedFormat('M j') }}</span>
        </x-home-task-row>
    @empty
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('home.empty') }}</p>
    @endforelse
</x-home-card>
