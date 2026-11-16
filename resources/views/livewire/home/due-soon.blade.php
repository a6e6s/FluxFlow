<x-home-card :title="__('home.widgets.due_soon')" icon="triangle-alert" tone="rose">
    <x-slot:badge>
        @if ($tasks->isNotEmpty())
            <x-home-pill tone="rose">{{ __('home.pills.overdue_count', ['count' => $tasks->filter(fn ($task) => $task->isOverdue())->count()]) }}</x-home-pill>
        @endif
    </x-slot:badge>

    @forelse ($tasks as $task)
        <x-home-task-row :task="$task">
            @if ($task->isOverdue())
                <x-home-pill tone="rose">{{ __('home.labels.overdue') }} · {{ $task->due_date->translatedFormat('M j') }}</x-home-pill>
            @else
                <x-home-pill tone="amber">{{ __('home.labels.today') }}</x-home-pill>
            @endif
        </x-home-task-row>
    @empty
        <x-home-empty icon="circle-check" :title="__('home.states.due_soon_title')">
            {{ __('home.states.due_soon_text') }}
        </x-home-empty>
    @endforelse
</x-home-card>
