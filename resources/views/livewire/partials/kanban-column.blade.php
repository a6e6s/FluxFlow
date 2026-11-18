{{-- Kanban Column Partial --}}
@props([
    'status',
    'title',
    'tasks',
    'dotColor' => 'bg-slate-400',
    'emptyIcon' => 'circle',
])

<div data-board-column="{{ $status }}" class="flex flex-col w-[85vw] max-w-sm snap-center md:w-80 md:max-w-none h-full shrink-0 {{ $status === 'done' ? 'opacity-70 hover:opacity-100 transition-opacity' : '' }}">
    {{-- Column Header --}}
    <div class="flex items-center justify-between mb-4 px-1">
        <div class="flex items-center gap-2">
            <div class="size-2 rounded-full {{ $dotColor }}"></div>
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                {{ $title }}
            </h3>
            <span class="px-2 py-0.5 rounded-full bg-slate-200 dark:bg-[#283239] text-xs font-medium text-slate-600 dark:text-slate-400">
                {{ $tasks->count() }}
            </span>
        </div>
        <button class="text-slate-400 hover:text-white transition-colors">
            <x-lucide-more-horizontal class="size-4" />
        </button>
    </div>

    {{-- Tasks Container (Sortable) --}}
    <div
        data-column="{{ $status }}"
        class="kanban-column overflow-y-auto space-y-3 pr-2"
        style="height: calc(100vh - 260px);"
    >
        @forelse($tasks as $task)
            @include('livewire.partials.task-card', ['task' => $task])
        @empty
            {{-- Empty State - still allows dropping --}}
            <div class="empty-state flex flex-col items-center justify-center p-4 border-2 border-dashed border-slate-200 dark:border-[#283239] rounded-xl bg-slate-50 dark:bg-[#101a22]/50 min-h-[150px] pointer-events-none">
                <div class="p-3 bg-slate-100 dark:bg-[#1c2630] rounded-full mb-3">
                    @switch($emptyIcon)
                        @case('circle')
                            <x-lucide-circle class="size-5 text-slate-400" />
                            @break
                        @case('circle-dot')
                            <x-lucide-circle-dot class="size-5 text-slate-400" />
                            @break
                        @case('eye')
                            <x-lucide-eye class="size-5 text-slate-400" />
                            @break
                        @case('circle-check')
                            <x-lucide-circle-check class="size-5 text-slate-400" />
                            @break
                        @default
                            <x-lucide-circle class="size-5 text-slate-400" />
                    @endswitch
                </div>
                <span class="text-sm text-slate-500 font-medium">{{ __('app.no_tasks') }}</span>
            </div>
        @endforelse
    </div>
</div>
