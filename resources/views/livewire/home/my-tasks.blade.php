<x-home-card :title="__('home.widgets.my_tasks')" icon="user-check" tone="emerald">
    @forelse ($tasks as $task)
        <x-home-task-row :task="$task">
            <span class="rounded px-1.5 py-0.5 text-[11px] font-semibold {{ $task->status->bgColor() }} {{ $task->status->color() }}">{{ $task->status->label() }}</span>
            <span class="rounded px-1.5 py-0.5 text-[11px] font-semibold {{ $task->priority->bgColor() }} {{ $task->priority->color() }}">{{ $task->priority->label() }}</span>
        </x-home-task-row>
    @empty
        <x-home-empty icon="circle-check" :title="__('home.states.tasks_title')">
            {{ __('home.states.tasks_text') }}

            <x-slot:action>
                <a href="{{ route('dashboard') }}" wire:navigate
                    class="mt-3 inline-flex rounded-lg bg-[#1392ec] px-3 py-1.5 text-xs font-semibold text-white shadow-lg shadow-blue-500/20 hover:bg-blue-600">
                    {{ __('home.states.browse') }}
                </a>
            </x-slot:action>
        </x-home-empty>
    @endforelse
</x-home-card>
