{{-- Create Task Modal --}}
<div
    x-data="{
        open: $wire.entangle('open'),
        description: $wire.entangle('description'),
        descriptionEditor: null,
        dragging: false,
        mountDescriptionEditor(element) {
            if (!window.Quill) {
                window.addEventListener('quill:ready', () => this.mountDescriptionEditor(element), { once: true });

                return;
            }

            if (this.descriptionEditor?.root.parentElement === element) {
                return;
            }

            this.descriptionEditor = window.Alpine.raw(new window.Quill(element, {
                theme: 'snow',
                placeholder: element.dataset.placeholder,
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ header: [1, 2, false] }],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['blockquote', 'link'],
                        ['clean'],
                    ],
                },
            }));

            this.descriptionEditor.root.innerHTML = this.description || '';
            this.descriptionEditor.on('text-change', (_delta, _oldDelta, source) => {
                if (source === 'user') {
                    this.description = this.descriptionEditor.root.innerHTML;
                }
            });
        },
        refreshDescriptionEditor() {
            if (this.descriptionEditor && this.descriptionEditor.root.innerHTML !== (this.description || '')) {
                this.descriptionEditor.root.innerHTML = this.description || '';
            }
        }
    }"
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
            class="relative w-full max-w-xl rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-[#283239] dark:bg-[#101a22]"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-[#283239]">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-[#1392ec]/10 p-2">
                        <x-lucide-clipboard-list class="size-5 text-[#1392ec]" />
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
            <form wire:submit="create" class="space-y-4 p-4 sm:p-5">
                {{-- Task Title --}}
                <div>
                    <input
                        type="text"
                        wire:model="title"
                        x-init="$nextTick(() => { if (open) $el.focus() })"
                        x-effect="if (open) $el.focus()"
                        placeholder="{{ __('app.enter_task_title') }}"
                        class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 placeholder-slate-500 transition-colors focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] dark:border-[#283239] dark:bg-[#1c2630] dark:text-white dark:placeholder-slate-500"
                    />
                    @error('title')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <div class="task-description-editor overflow-hidden rounded-lg border border-slate-200 bg-slate-50 transition-colors focus-within:border-[#1392ec] focus-within:ring-1 focus-within:ring-[#1392ec] dark:border-[#283239] dark:bg-[#1c2630]">
                        <div wire:ignore x-init="mountDescriptionEditor($el)"
                            x-effect="refreshDescriptionEditor()"
                            data-placeholder="{{ __('app.description_placeholder') }}"></div>
                    </div>
                </div>

                {{-- Compact Task Metadata --}}
                <div class="grid grid-cols-3 items-end gap-2">
                    {{-- Due Date --}}
                    <div>
                        <label class="mb-1 block truncate text-[10px] font-medium text-slate-600 dark:text-slate-400">{{ __('app.due_date') }}</label>
                        <input type="date" wire:model="dueDate" title="{{ __('app.due_date') }}"
                            class="w-full min-w-0 rounded-lg border border-slate-200 bg-slate-50 px-1.5 py-2 text-xs text-slate-900 transition-colors focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] [color-scheme:light] dark:border-[#283239] dark:bg-[#1c2630] dark:text-white dark:[color-scheme:dark]" />
                    </div>

                    {{-- Effort Score --}}
                    <div>
                        <label class="mb-1 block truncate text-[10px] font-medium text-slate-600 dark:text-slate-400">{{ __('app.effort_score') }}</label>
                        <input type="number" wire:model="effortScore" min="1" max="10" placeholder="1-10"
                            title="{{ __('app.effort_score') }}"
                            class="w-full min-w-0 rounded-lg border border-slate-200 bg-slate-50 px-2 py-2 text-xs text-slate-900 placeholder-slate-500 transition-colors focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] dark:border-[#283239] dark:bg-[#1c2630] dark:text-white dark:placeholder-slate-500" />
                    </div>

                    {{-- Priority --}}
                    <div class="min-w-0">
                        <label class="mb-1 block truncate text-[10px] font-medium text-slate-600 dark:text-slate-400">{{ __('app.priority') }}</label>
                        <div class="flex min-w-0 gap-1">
                            @foreach (['low' => ['label' => __('app.low'), 'color' => 'blue'], 'medium' => ['label' => __('app.medium'), 'color' => 'amber'], 'high' => ['label' => __('app.high'), 'color' => 'red']] as $value => $config)
                                <button type="button" wire:click="$set('priority', '{{ $value }}')"
                                    @class([
                                        'min-w-0 flex-1 rounded-lg px-1 py-2 text-[9px] font-bold uppercase transition-all',
                                        'bg-' . $config['color'] . '-500/20 text-' . $config['color'] . '-400 ring-1 ring-' . $config['color'] . '-500' => $priority === $value,
                                        'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 dark:bg-[#101a22] dark:text-slate-500 dark:hover:bg-[#283239] dark:hover:text-white' => $priority !== $value,
                                    ])>
                                    {{ $config['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="grid grid-cols-2 items-center gap-2">
                    <label for="create-task-status" class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ __('app.status') }}</label>
                    <select id="create-task-status" wire:model="status"
                        class="w-full rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 text-xs text-slate-900 transition-colors focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] dark:border-[#283239] dark:bg-[#1c2630] dark:text-white">
                        <option value="todo">{{ __('app.todo') }}</option>
                        <option value="doing">{{ __('app.doing') }}</option>
                        <option value="review">{{ __('app.review') }}</option>
                        <option value="done">{{ __('app.done') }}</option>
                    </select>
                </div>

                {{-- Attachments --}}
                <div>
                    <div class="mb-1.5 flex items-center justify-between">
                        <label class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ __('app.attachments') }}</label>
                        <span class="text-[10px] text-slate-500">{{ __('app.file_types_limit') }}</span>
                    </div>
                    <div
                        x-on:dragover.prevent="dragging = true"
                        x-on:dragleave.prevent="dragging = false"
                        x-on:drop.prevent="dragging = false; $wire.uploadMultiple('files', $event.dataTransfer.files);"
                        :class="dragging ? 'border-[#1392ec] bg-[#1392ec]/5' : 'border-slate-200 dark:border-[#283239]'"
                        class="relative rounded-lg border border-dashed bg-slate-50 p-2.5 text-center transition-colors hover:border-[#1392ec]/50 dark:bg-[#101a22]/50"
                    >
                        <input type="file" wire:model="files" multiple
                            class="absolute inset-0 h-full w-full cursor-pointer opacity-0" />
                        <div class="flex items-center justify-center gap-2">
                            <x-lucide-cloud-upload class="size-4 text-slate-400" />
                            <p class="text-xs text-slate-600 dark:text-slate-400">
                                <span class="font-medium text-[#1392ec]">{{ __('app.click_to_upload') }}</span>
                                {{ __('app.or_drag_drop') }}
                            </p>
                        </div>
                    </div>
                    <div wire:loading wire:target="files" class="mt-2 text-xs text-slate-500">
                        {{ __('app.uploading_files') }}
                    </div>
                    @error('files.*')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                    @if (count($files) > 0)
                        <div class="mt-2 space-y-1.5">
                            @foreach ($files as $index => $file)
                                <div wire:key="create-task-file-{{ $index }}"
                                    class="flex items-center justify-between gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-2 dark:border-[#283239] dark:bg-[#1c2630]">
                                    <div class="flex min-w-0 items-center gap-2">
                                        <x-lucide-file class="size-4 shrink-0 text-amber-500" />
                                        <span class="truncate text-xs text-slate-700 dark:text-slate-300">{{ $file->getClientOriginalName() }}</span>
                                        <span class="shrink-0 text-[10px] text-slate-500">{{ number_format($file->getSize() / 1024, 1) }} KB</span>
                                    </div>
                                    <button type="button" wire:click="removeTempFile({{ $index }})"
                                        class="shrink-0 rounded p-1 text-slate-500 transition-colors hover:text-red-400"
                                        aria-label="{{ __('app.delete') }}">
                                        <x-lucide-x class="size-3.5" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between border-t border-slate-200 pt-3 dark:border-[#283239]">
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
