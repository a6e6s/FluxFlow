{{-- Global Search Modal --}}
<div
    x-data="{ open: $wire.entangle('open') }"
    x-show="open"
    x-cloak
    @keydown.escape.window="if (open) $wire.close()"
    @keydown.meta.k.window.prevent="$wire.openSearch()"
    @keydown.ctrl.k.window.prevent="$wire.openSearch()"
    class="fixed inset-0 z-[60] overflow-y-auto"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="$wire.close()"
        class="fixed inset-0 bg-black/70 backdrop-blur-sm"
    ></div>

    {{-- Modal --}}
    <div class="flex min-h-full items-start justify-center p-4 pt-[15vh]">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.stop
            class="relative w-full max-w-2xl bg-[#1c2630] rounded-2xl shadow-2xl border border-[#283239] overflow-hidden"
        >
            {{-- Search Input --}}
            <div class="relative border-b border-[#283239]">
                <x-lucide-search class="absolute left-5 top-1/2 -translate-y-1/2 size-5 text-slate-400" />
                <input
                    type="text"
                    wire:model.live.debounce.300ms="query"
                    x-init="$nextTick(() => $el.focus())"
                    placeholder="{{ __('app.search_placeholder') }}"
                    class="w-full bg-transparent border-none py-5 pl-14 pr-5 text-lg text-white placeholder-slate-500 focus:ring-0"
                />
                <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                    <span class="text-xs text-slate-500 border border-slate-600 rounded px-1.5 py-0.5">ESC</span>
                </div>
            </div>

            {{-- Results --}}
            <div class="max-h-[60vh] overflow-y-auto">
                @if(strlen($query) >= 2)
                    {{-- Projects --}}
                    @if($this->results['projects']->count() > 0)
                        <div class="p-3">
                            <h3 class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('app.projects') }}</h3>
                            <div class="space-y-1">
                                @foreach($this->results['projects'] as $project)
                                    <button
                                        wire:click="selectProject({{ $project->id }})"
                                        wire:key="search-project-{{ $project->id }}"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#283239] transition-colors text-left group"
                                    >
                                        <div
                                            class="size-8 rounded-lg flex items-center justify-center text-white"
                                            style="background-color: {{ $project->color ?? '#3b82f6' }}20"
                                        >
                                            @if($project->icon)
                                                <span>{{ $project->icon }}</span>
                                            @else
                                                <x-lucide-folder class="size-4" style="color: {{ $project->color ?? '#3b82f6' }}" />
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-white font-medium truncate group-hover:text-[#1392ec] transition-colors">
                                                {{ $project->title }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ __('app.priority_label', ['priority' => $project->priority->label()]) }}
                                            </p>
                                        </div>
                                        <x-lucide-arrow-right class="size-4 text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity" />
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Tasks --}}
                    @if($this->results['tasks']->count() > 0)
                        <div class="p-3 border-t border-[#283239]">
                            <h3 class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('app.tasks') }}</h3>
                            <div class="space-y-1">
                                @foreach($this->results['tasks'] as $task)
                                    <button
                                        wire:click="selectTask({{ $task->id }})"
                                        wire:key="search-task-{{ $task->id }}"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-[#283239] transition-colors text-left group"
                                    >
                                        @php
                                            $statusColors = [
                                                'todo' => 'bg-slate-500',
                                                'doing' => 'bg-[#1392ec]',
                                                'review' => 'bg-amber-500',
                                                'done' => 'bg-emerald-500',
                                            ];
                                        @endphp
                                        <div class="size-2 rounded-full {{ $statusColors[$task->status->value] ?? 'bg-slate-500' }}"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-white font-medium truncate group-hover:text-[#1392ec] transition-colors">
                                                {{ $task->title }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ __('app.in') }} {{ $task->project?->title ?? __('app.no_project') }}
                                            </p>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase px-2 py-1 rounded {{ $task->status->bgColor() }} {{ $task->status->color() }}">
                                            {{ $task->status->label() }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- No Results --}}
                    @if($this->results['projects']->count() === 0 && $this->results['tasks']->count() === 0)
                        <div class="p-12 text-center">
                            <x-lucide-search-x class="size-12 text-slate-600 mx-auto mb-4" />
                            <p class="text-slate-400 font-medium">{{ __('app.no_results') }}</p>
                            <p class="text-sm text-slate-500 mt-1">{{ __('app.type_to_search') }}</p>
                        </div>
                    @endif
                @else
                    {{-- Empty State --}}
                    <div class="p-12 text-center">
                        <x-lucide-sparkles class="size-12 text-slate-600 mx-auto mb-4" />
                        <p class="text-slate-400 font-medium">{{ __('app.start_typing') }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ __('app.search_description') }}</p>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between px-5 py-3 border-t border-[#283239] bg-[#101a22]/50">
                <div class="flex items-center gap-4 text-xs text-slate-500">
                    <span class="flex items-center gap-1">
                        <span class="border border-slate-600 rounded px-1.5 py-0.5">↵</span>
                        {{ __('app.select') }}
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="border border-slate-600 rounded px-1.5 py-0.5">ESC</span>
                        {{ __('app.close') }}
                    </span>
                </div>
                <div class="text-xs text-slate-500">
                    <span class="text-[#1392ec]">⌘K</span> {{ __('app.open_anytime') }}
                </div>
            </div>
        </div>
    </div>
</div>
