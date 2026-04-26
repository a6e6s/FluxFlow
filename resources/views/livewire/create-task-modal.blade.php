{{-- Create Task Modal --}}
<div
    x-data="{ open: $wire.entangle('open') }"
    x-show="open"
    x-cloak
    @keydown.escape.window="if (open) $wire.close()"
    class="fixed inset-0 z-50 overflow-y-auto"
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
        class="fixed inset-0 bg-black/60 backdrop-blur-sm"
    ></div>

    {{-- Modal --}}
    <div class="flex min-h-full items-center justify-center p-4">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            @click.stop
            class="relative w-full max-w-lg rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-[#283239] dark:bg-[#1c2630]"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-[#283239]">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-500/10 rounded-lg">
                        <x-lucide-plus-circle class="size-5 text-emerald-500" />
                    </div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ __('app.new_task') }}</h2>
                </div>
                <button
                    @click="$wire.close()"
                    class="p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-[#283239] transition-colors"
                >
                    <x-lucide-x class="size-5" />
                </button>
            </div>

            {{-- Form --}}
            <form wire:submit="create" class="p-6 space-y-5">
                {{-- Task Title --}}
                <div>
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">{{ __('app.task_title') }}</label>
                    <input
                        type="text"
                        wire:model="title"
                        x-init="$nextTick(() => { if (open) $el.focus() })"
                        x-effect="if (open) $el.focus()"
                        placeholder="{{ __('app.enter_task_title') }}"
                        class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-900 placeholder-slate-500 focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] transition-colors dark:border-[#283239] dark:bg-[#101a22] dark:text-white dark:placeholder-slate-500"
                    />
                    @error('title')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">{{ __('app.description') }}</label>
                    <textarea
                        wire:model="description"
                        rows="3"
                        placeholder="{{ __('app.description_placeholder') }}"
                        class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-900 placeholder-slate-500 focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] transition-colors resize-none dark:border-[#283239] dark:bg-[#101a22] dark:text-white dark:placeholder-slate-500"
                    ></textarea>
                </div>

                {{-- Priority & Status Row --}}
                <div class="grid grid-cols-2 gap-4">
                    {{-- Priority --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">{{ __('app.priority') }}</label>
                        <div class="flex gap-1.5">
                            @foreach(['low' => ['label' => 'Low', 'color' => 'blue'], 'medium' => ['label' => 'Med', 'color' => 'amber'], 'high' => ['label' => 'High', 'color' => 'red']] as $value => $config)
                                <button
                                    type="button"
                                    wire:click="$set('priority', '{{ $value }}')"
                                    @class([
                                        'flex-1 px-3 py-2 rounded-lg text-xs font-bold uppercase transition-all',
                                        'bg-' . $config['color'] . '-500/20 text-' . $config['color'] . '-400 ring-1 ring-' . $config['color'] . '-500' => $priority === $value,
                                        'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 dark:bg-[#101a22] dark:text-slate-500 dark:hover:bg-[#283239] dark:hover:text-white' => $priority !== $value,
                                    ])
                                >
                                    {{ $config['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">{{ __('app.status') }}</label>
                        <select
                            wire:model="status"
                            class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-900 focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] transition-colors dark:border-[#283239] dark:bg-[#101a22] dark:text-white"
                        >
                            <option value="todo">{{ __('app.todo') }}</option>
                            <option value="doing">{{ __('app.doing') }}</option>
                            <option value="review">{{ __('app.review') }}</option>
                            <option value="done">{{ __('app.done') }}</option>
                        </select>
                    </div>
                </div>

                {{-- Due Date & Effort --}}
                <div class="grid grid-cols-2 gap-4">
                    {{-- Due Date --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">{{ __('app.due_date') }}</label>
                        <div class="relative">
                            <x-lucide-calendar class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-500 pointer-events-none" />
                            <input
                                type="date"
                                wire:model="dueDate"
                                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-900 focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] transition-colors [color-scheme:light] dark:border-[#283239] dark:bg-[#101a22] dark:text-white dark:[color-scheme:dark]"
                            />
                        </div>
                    </div>

                    {{-- Effort Score --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">{{ __('app.effort_score') }}</label>
                        <div class="relative">
                            <x-lucide-gauge class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-500 pointer-events-none" />
                            <input
                                type="number"
                                wire:model="effortScore"
                                min="1"
                                max="10"
                                placeholder="Points"
                                class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-900 placeholder-slate-500 focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] transition-colors dark:border-[#283239] dark:bg-[#101a22] dark:text-white dark:placeholder-slate-500"
                            />
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-2">
                        <p class="text-xs text-slate-500 dark:text-slate-500">
                        Press <span class="border border-slate-600 rounded px-1.5 py-0.5">N</span> to create quickly
                    </p>
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            @click="$wire.close()"
                            class="px-4 py-2.5 text-sm font-medium text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors"
                        >
                            {{ __('app.cancel') }}
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium rounded-lg transition-colors shadow-lg shadow-emerald-500/20 disabled:opacity-50 flex items-center gap-2"
                        >
                            <span wire:loading.remove wire:target="create">{{ __('app.create_task') }}</span>
                            <span wire:loading wire:target="create">
                                <svg class="animate-spin size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
