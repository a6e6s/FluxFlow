{{-- Task Card Partial --}}
@props(['task'])

@php
    $priorityColors = [
        'high' => [
            'bg' => 'bg-red-500/10',
            'text' => 'text-red-500',
            'border' => 'bg-red-500',
        ],
        'medium' => [
            'bg' => 'bg-amber-500/10',
            'text' => 'text-amber-500',
            'border' => 'bg-amber-500',
        ],
        'low' => [
            'bg' => 'bg-blue-400/10',
            'text' => 'text-blue-400',
            'border' => 'bg-blue-400',
        ],
    ];

    $colors = $priorityColors[$task->priority->value] ?? $priorityColors['medium'];
    $isDone = $task->status->value === 'done';
@endphp

<div
    data-task-id="{{ $task->id }}"
    wire:key="task-{{ $task->id }}"
    @click="$dispatch('open-task-details', { taskId: {{ $task->id }} })"
    class="group draggable-source bg-white dark:bg-[#1c2630] p-4 rounded-xl border border-slate-200 dark:border-[#283239] hover:border-[#1392ec]/50 hover:shadow-glow transition-all duration-200 cursor-grab relative overflow-hidden"
>
    {{-- Priority Border --}}
    <div class="absolute left-0 top-0 bottom-0 w-1 {{ $isDone ? 'bg-emerald-500' : $colors['border'] }}"></div>

    {{-- Header Row --}}
    <div class="flex justify-between items-start mb-2">
        {{-- Priority Badge --}}
        @if($isDone)
            <span class="px-2 py-1 rounded bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-wider">
                Done
            </span>
        @else
            <span class="px-2 py-1 rounded {{ $colors['bg'] }} {{ $colors['text'] }} text-[10px] font-bold uppercase tracking-wider">
                {{ $task->priority->label() }}
            </span>
        @endif

        {{-- Actions --}}
        <div class="flex items-center gap-1">
            @if($isDone)
                <x-lucide-check class="size-4 text-emerald-500" />
            @else
                <button class="opacity-0 group-hover:opacity-100 p-1 rounded hover:bg-slate-700 transition-all">
                    <x-lucide-pencil class="size-3.5 text-slate-400 hover:text-[#1392ec]" />
                </button>
            @endif
        </div>
    </div>

    {{-- Task Title --}}
    <h4 @class([
        'text-sm font-semibold leading-snug mb-3',
        'text-slate-500 dark:text-slate-400 line-through' => $isDone,
        'text-slate-800 dark:text-white' => !$isDone,
    ])>
        {{ $task->title }}
    </h4>

    {{-- Due Date Warning (if overdue or due today) --}}
    @if(!$isDone && $task->due_date)
        @if($task->isOverdue())
            <div class="flex items-center gap-1.5 mb-3 text-red-400">
                <x-lucide-alert-circle class="size-4" />
                <span class="text-xs font-medium">Overdue</span>
            </div>
        @elseif($task->isDueToday())
            <div class="px-2 py-1 rounded bg-red-500/20 text-red-400 text-[10px] font-bold inline-block mb-3">
                Due Today
            </div>
        @endif
    @endif

    {{-- Footer Row --}}
    <div class="flex items-center justify-between mt-4">
        {{-- Left Side: Due date / Completed info --}}
        <div class="flex items-center gap-3">
            @if($isDone)
                <span class="text-xs text-slate-500 font-medium">
                    Completed {{ $task->updated_at->diffForHumans() }}
                </span>
            @elseif($task->due_date)
                <div class="flex items-center gap-1.5 {{ $task->isOverdue() ? 'text-red-400' : 'text-slate-500' }}">
                    <x-lucide-calendar class="size-4" />
                    <span class="text-xs font-medium">{{ $task->due_date->format('M d') }}</span>
                </div>
            @endif

            {{-- Attachments Count --}}
            @if($task->attachments_count > 0)
                <div class="flex items-center gap-1 text-slate-500" title="{{ $task->attachments_count }} attachment(s)">
                    <x-lucide-paperclip class="size-3.5" />
                    <span class="text-xs">{{ $task->attachments_count }}</span>
                </div>
            @endif
        </div>

        {{-- Right Side: Effort Score & Assignee --}}
        <div class="flex items-center gap-2">
            {{-- Effort Score --}}
            @if($task->effort_score)
                <div class="flex items-center text-slate-500" title="Effort: {{ $task->effort_score }} points">
                    <x-lucide-gauge class="size-4" />
                    <span class="text-xs ml-0.5">{{ $task->effort_score }}</span>
                </div>
            @endif

            {{-- Assignee Avatar --}}
            @if($task->assignee)
                <div
                    class="size-6 rounded-full bg-cover bg-center {{ $isDone ? 'grayscale opacity-60' : '' }}"
                    title="{{ $task->assignee->name }}"
                    @if($task->assignee->profile_photo_path)
                        style="background-image: url('{{ $task->assignee->profile_photo_url }}')"
                    @else
                        style="background-color: #1392ec"
                    @endif
                >
                    @unless($task->assignee->profile_photo_path)
                        <div class="size-full flex items-center justify-center text-[10px] font-semibold text-white">
                            {{ $task->assignee->initials() }}
                        </div>
                    @endunless
                </div>
            @else
                <div class="size-6 rounded-full bg-slate-700 flex items-center justify-center text-[10px] text-slate-400" title="Unassigned">
                    ?
                </div>
            @endif
        </div>
    </div>
</div>
