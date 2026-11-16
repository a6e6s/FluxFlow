<x-home-card :title="__('home.widgets.upcoming')" icon="calendar">
    @forelse ($tasks as $task)
        <x-home-task-row :task="$task">
            <x-home-pill tone="primary">{{ $task->due_date->translatedFormat('M j') }}</x-home-pill>
        </x-home-task-row>
    @empty
        <x-home-empty icon="calendar" :title="__('home.states.upcoming_title')">
            {{ __('home.states.upcoming_text') }}
        </x-home-empty>
    @endforelse
</x-home-card>
