<x-home-card :title="__('home.widgets.my_tasks')" icon="user-check">
    @forelse ($tasks as $task)
        <x-home-task-row :task="$task">
            <span class="rounded px-1.5 py-0.5 text-[11px] font-semibold {{ $task->status->bgColor() }} {{ $task->status->color() }}">{{ $task->status->label() }}</span>
            <span class="rounded px-1.5 py-0.5 text-[11px] font-semibold {{ $task->priority->bgColor() }} {{ $task->priority->color() }}">{{ $task->priority->label() }}</span>
        </x-home-task-row>
    @empty
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('home.empty') }}</p>
    @endforelse
</x-home-card>
