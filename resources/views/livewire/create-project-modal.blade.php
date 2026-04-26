{{-- Create Project Modal --}}
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
            class="relative w-full max-w-md rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-[#283239] dark:bg-[#1c2630]"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-[#283239]">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-[#1392ec]/10 rounded-lg">
                        <x-lucide-folder-plus class="size-5 text-[#1392ec]" />
                    </div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ __('app.new_project') }}</h2>
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
                {{-- Project Title --}}
                <div>
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">{{ __('app.project_name') }}</label>
                    <input
                        type="text"
                        wire:model="title"
                        x-init="$nextTick(() => { if (open) $el.focus() })"
                        x-effect="if (open) $el.focus()"
                        placeholder="{{ __('app.enter_project_name') }}"
                        class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50 text-slate-900 placeholder-slate-500 focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] transition-colors dark:border-[#283239] dark:bg-[#101a22] dark:text-white dark:placeholder-slate-500"
                    />
                    @error('title')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Icon Picker --}}
                <div>
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">{{ __('app.icon_optional') }}</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($iconOptions as $iconOption)
                            <button
                                type="button"
                                wire:click="$set('icon', '{{ $iconOption }}')"
                                @class([
                                    'size-10 flex items-center justify-center rounded-lg text-lg transition-all',
                                    'bg-[#1392ec]/20 ring-2 ring-[#1392ec]' => $icon === $iconOption,
                                    'bg-slate-100 hover:bg-slate-200 dark:bg-[#101a22] dark:hover:bg-[#283239]' => $icon !== $iconOption,
                                ])
                            >
                                {{ $iconOption }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Color Picker --}}
                <div>
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">{{ __('app.color') }}</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($colorOptions as $colorOption)
                            <button
                                type="button"
                                wire:click="$set('color', '{{ $colorOption }}')"
                                @class([
                                    'size-8 rounded-full transition-all',
                                    'ring-2 ring-offset-2 ring-offset-white dark:ring-offset-[#1c2630]' => $color === $colorOption,
                                ])
                                style="background-color: {{ $colorOption }}; {{ $color === $colorOption ? 'ring-color: ' . $colorOption : '' }}"
                            ></button>
                        @endforeach
                    </div>
                </div>

                {{-- Priority --}}
                <div>
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">{{ __('app.priority') }}</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['low' => __('app.low'), 'medium' => __('app.medium'), 'high' => __('app.high')] as $value => $label)
                            <button
                                type="button"
                                wire:click="$set('priority', '{{ $value }}')"
                                @class([
                                    'px-4 py-2.5 rounded-lg text-sm font-medium transition-all',
                                    'bg-[#1392ec] text-white' => $priority === $value,
                                    'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 dark:bg-[#101a22] dark:text-slate-400 dark:hover:bg-[#283239] dark:hover:text-white' => $priority !== $value,
                                ])
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Preview --}}
                <div class="p-4 rounded-lg border border-slate-200 bg-slate-50 dark:border-[#283239] dark:bg-[#101a22]">
                    <p class="text-xs text-slate-500 dark:text-slate-500 mb-2">{{ __('app.preview') }}</p>
                    <div class="flex items-center gap-3">
                        <div
                            class="size-10 rounded-lg flex items-center justify-center text-lg"
                            style="background-color: {{ $color }}20"
                        >
                            @if($icon)
                                {{ $icon }}
                            @else
                                <x-lucide-folder class="size-5" style="color: {{ $color }}" />
                            @endif
                        </div>
                        <div>
                            <p class="font-medium text-slate-900 dark:text-white">{{ $title ?: 'Project Name' }}</p>
                            <p class="text-xs" style="color: {{ $color }}">{{ ucfirst($priority) }} Priority</p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-2">
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
                        class="px-6 py-2.5 bg-[#1392ec] hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors shadow-lg shadow-blue-500/20 disabled:opacity-50 flex items-center gap-2"
                    >
                        <span wire:loading.remove wire:target="create">{{ __('app.create_project') }}</span>
                        <span wire:loading wire:target="create">
                            <svg class="animate-spin size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
