{{-- Edit Project Modal --}}
<div
    x-data="{ open: $wire.entangle('open'), confirmDelete: false }"
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
            class="relative w-full max-w-md bg-[#1c2630] rounded-2xl shadow-2xl border border-[#283239]"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#283239]">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-[#1392ec]/10 rounded-lg">
                        <x-lucide-folder-pen class="size-5 text-[#1392ec]" />
                    </div>
                    <h2 class="text-lg font-semibold text-white">Edit Project</h2>
                </div>
                <button
                    @click="$wire.close()"
                    class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-[#283239] transition-colors"
                >
                    <x-lucide-x class="size-5" />
                </button>
            </div>

            {{-- Form --}}
            <form wire:submit="update" class="p-6 space-y-5">
                {{-- Project Title --}}
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Project Name</label>
                    <input
                        type="text"
                        wire:model="title"
                        x-init="$nextTick(() => { if (open) $el.focus() })"
                        x-effect="if (open) $el.focus()"
                        placeholder="Enter project name..."
                        class="w-full px-4 py-3 bg-[#101a22] border border-[#283239] rounded-lg text-white placeholder-slate-500 focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] transition-colors"
                    />
                    @error('title')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Icon Picker --}}
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Icon (optional)</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($iconOptions as $iconOption)
                            <button
                                type="button"
                                wire:click="$set('icon', '{{ $iconOption }}')"
                                @class([
                                    'size-10 flex items-center justify-center rounded-lg text-lg transition-all',
                                    'bg-[#1392ec]/20 ring-2 ring-[#1392ec]' => $icon === $iconOption,
                                    'bg-[#101a22] hover:bg-[#283239]' => $icon !== $iconOption,
                                ])
                            >
                                {{ $iconOption }}
                            </button>
                        @endforeach
                        {{-- Clear icon button --}}
                        <button
                            type="button"
                            wire:click="$set('icon', '')"
                            @class([
                                'size-10 flex items-center justify-center rounded-lg text-lg transition-all',
                                'bg-[#1392ec]/20 ring-2 ring-[#1392ec]' => $icon === '',
                                'bg-[#101a22] hover:bg-[#283239]' => $icon !== '',
                            ])
                            title="No icon"
                        >
                            <x-lucide-x class="size-4 text-slate-400" />
                        </button>
                    </div>
                </div>

                {{-- Color Picker --}}
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Color</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($colorOptions as $colorOption)
                            <button
                                type="button"
                                wire:click="$set('color', '{{ $colorOption }}')"
                                @class([
                                    'size-8 rounded-full transition-all',
                                    'ring-2 ring-offset-2 ring-offset-[#1c2630]' => $color === $colorOption,
                                ])
                                style="background-color: {{ $colorOption }}; {{ $color === $colorOption ? 'ring-color: ' . $colorOption : '' }}"
                            ></button>
                        @endforeach
                    </div>
                </div>

                {{-- Priority --}}
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Priority</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label)
                            <button
                                type="button"
                                wire:click="$set('priority', '{{ $value }}')"
                                @class([
                                    'px-4 py-2.5 rounded-lg text-sm font-medium transition-all',
                                    'bg-[#1392ec] text-white' => $priority === $value,
                                    'bg-[#101a22] text-slate-400 hover:bg-[#283239] hover:text-white' => $priority !== $value,
                                ])
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Preview --}}
                <div class="p-4 bg-[#101a22] rounded-lg border border-[#283239]">
                    <p class="text-xs text-slate-500 mb-2">Preview</p>
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
                            <p class="text-white font-medium">{{ $title ?: 'Project Name' }}</p>
                            <p class="text-xs" style="color: {{ $color }}">{{ ucfirst($priority) }} Priority</p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-2">
                    {{-- Delete Button --}}
                    <div x-show="!confirmDelete">
                        <button
                            type="button"
                            @click="confirmDelete = true"
                            class="px-4 py-2.5 text-sm font-medium text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-colors"
                        >
                            Delete Project
                        </button>
                    </div>

                    {{-- Confirm Delete --}}
                    <div x-show="confirmDelete" x-cloak class="flex items-center gap-2">
                        <span class="text-sm text-red-400">Delete?</span>
                        <button
                            type="button"
                            wire:click="delete"
                            class="px-3 py-1.5 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors"
                        >
                            Yes
                        </button>
                        <button
                            type="button"
                            @click="confirmDelete = false"
                            class="px-3 py-1.5 text-sm font-medium text-slate-400 hover:text-white rounded-lg transition-colors"
                        >
                            No
                        </button>
                    </div>

                    {{-- Save/Cancel --}}
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            @click="$wire.close()"
                            class="px-4 py-2.5 text-sm font-medium text-slate-400 hover:text-white rounded-lg transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 bg-[#1392ec] hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2"
                        >
                            <x-lucide-check class="size-4" />
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
