{{-- Task Details Slide-over --}}
<div x-data="{
    open: $wire.entangle('open'),
    dragging: false
}" x-show="open" x-cloak @keydown.escape.window="if (open) $wire.close()"
    class="fixed inset-0 z-50 overflow-hidden">
    {{-- Backdrop --}}
    <div x-show="open" x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="$wire.close()"
        class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    {{-- Slide-over Panel --}}
    <div x-show="open" x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="ltr:translate-x-full rtl:-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-200" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="ltr:translate-x-full rtl:-translate-x-full" class="absolute inset-y-0 ltr:right-0 rtl:left-0 w-full max-w-xl flex">
        <div class="relative flex w-full flex-col ltr:border-l rtl:border-r border-slate-200 bg-white shadow-2xl dark:border-[#283239] dark:bg-[#101a22]">
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-[#283239]">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-[#1392ec]/10 rounded-lg">
                        <x-lucide-clipboard-list class="size-5 text-[#1392ec]" />
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ __('app.task_details') }}</h2>
                        @if ($this->task)
                            <p class="text-xs text-slate-500 dark:text-slate-500">{{ $this->task->project->title }}</p>
                        @endif
                    </div>
                </div>
                <button @click="$wire.close()"
                    class="p-2 rounded-lg text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-[#283239] dark:hover:text-white">
                    <x-lucide-x class="size-5" />
                </button>
            </div>

            @if ($this->task)
                {{-- Content --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    {{-- Title --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-600 dark:text-slate-400">{{ __('app.title') }}</label>
                        <input type="text" wire:model="title"
                            @readonly(! $this->isOwner)
                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder-slate-500 transition-colors focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] dark:border-[#283239] dark:bg-[#1c2630] dark:text-white dark:placeholder-slate-500 read-only:opacity-70 read-only:cursor-not-allowed"
                            placeholder="{{ __('app.title_placeholder') }}" />
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-600 dark:text-slate-400">{{ __('app.description') }}</label>
                        <textarea wire:model="description" rows="4"
                            @readonly(! $this->canEditDescription)
                            class="w-full resize-none rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder-slate-500 transition-colors focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] dark:border-[#283239] dark:bg-[#1c2630] dark:text-white dark:placeholder-slate-500 read-only:opacity-70 read-only:cursor-not-allowed"
                            placeholder="{{ __('app.description_placeholder') }}"></textarea>
                    </div>

                    {{-- Due Date & Assignee Row --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Due Date --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-600 dark:text-slate-400">{{ __('app.due_date') }}</label>
                            <div class="relative">
                                <x-lucide-calendar
                                    class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-500 pointer-events-none" />
                                <input type="date" wire:model="dueDate"
                                    @readonly(! $this->isOwner)
                                    class="w-full rounded-lg border border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-slate-900 transition-colors focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] [color-scheme:light] dark:border-[#283239] dark:bg-[#1c2630] dark:text-white dark:[color-scheme:dark] read-only:opacity-70 read-only:cursor-not-allowed" />
                            </div>
                        </div>

                        {{-- Effort Score --}}
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-600 dark:text-slate-400">{{ __('app.effort_score') }}</label>
                            <div class="relative">
                                <x-lucide-gauge
                                    class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-500 pointer-events-none" />
                                <input type="number" wire:model="effortScore" min="1" max="10"
                                    @readonly(! $this->isOwner)
                                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 pl-10 pr-4 text-slate-900 placeholder-slate-500 transition-colors focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] dark:border-[#283239] dark:bg-[#1c2630] dark:text-white dark:placeholder-slate-500 read-only:opacity-70 read-only:cursor-not-allowed"
                                    placeholder="1-10" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">

                        {{-- Priority --}}
                        <div class="col-span-2">
                            <label class="mb-2 block text-sm font-medium text-slate-600 dark:text-slate-400">{{ __('app.priority') }}</label>
                            <div class="flex gap-1.5">
                                @foreach (['low' => ['label' => __('app.low'), 'color' => 'blue'], 'medium' => ['label' => __('app.medium'), 'color' => 'amber'], 'high' => ['label' => __('app.high'), 'color' => 'red']] as $value => $config)
                                    <button type="button" wire:click="setPriority('{{ $value }}')"
                                        @disabled(! $this->isOwner)
                                        @class([
                                            'flex-1 px-3 py-2 rounded-lg text-xs font-bold uppercase transition-all',
                                            'bg-' .
                                            $config['color'] .
                                            '-500/20 text-' .
                                            $config['color'] .
                                            '-400 ring-1 ring-' .
                                            $config['color'] .
                                            '-500' => $priority->value === $value,
                                            'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 dark:bg-[#101a22] dark:text-slate-500 dark:hover:bg-[#283239] dark:hover:text-white' => $priority->value !== $value,
                                            'opacity-60 cursor-not-allowed' => ! $this->isOwner,
                                        ])>
                                        {{ $config['label'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-slate-200 dark:border-[#283239]"></div>

                    {{-- Collaborators / Invitations --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ __('invitations.collaborators') }}</label>
                            <span class="text-xs text-slate-500">{{ $this->collaborators->count() }}</span>
                        </div>

                        @if ($this->isOwner)
                            <div class="space-y-2">
                                <label for="invite-email" class="text-xs font-medium text-slate-500">{{ __('invitations.invite_label') }}</label>
                                <div class="flex gap-2">
                                    <input id="invite-email" type="email" wire:model.live.debounce.400ms="inviteEmail"
                                        placeholder="{{ __('invitations.invite_placeholder') }}"
                                        autocomplete="off"
                                        class="flex-1 rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-900 placeholder-slate-500 transition-colors focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] dark:border-[#283239] dark:bg-[#1c2630] dark:text-white" />
                                    <button type="button" wire:click="invite" wire:loading.attr="disabled" wire:target="invite"
                                        @disabled(in_array($this->invitePreview['state'] ?? 'idle', ['idle', 'owner', 'already'], true))
                                        class="px-4 py-2.5 bg-[#1392ec] hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                        <span wire:loading.remove wire:target="invite">{{ __('invitations.invite_button') }}</span>
                                        <span wire:loading wire:target="invite">{{ __('invitations.inviting') }}</span>
                                    </button>
                                </div>
                                @error('inviteEmail')
                                    <p class="text-xs text-red-500">{{ $message }}</p>
                                @enderror

                                {{-- Live Preview Card --}}
                                @php
                                    $preview = $this->invitePreview;
                                    $previewBorder = match ($preview['state'] ?? 'idle') {
                                        'existing' => 'border-emerald-500/30 bg-emerald-500/5',
                                        'owner', 'already' => 'border-amber-500/30 bg-amber-500/5',
                                        default => 'border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800',
                                    };
                                @endphp
                                @if (($preview['state'] ?? 'idle') !== 'idle')
                                    <div class="mt-2 flex items-center gap-3 rounded-lg border p-3 {{ $previewBorder }}">
                                        @if (! empty($preview['user']))
                                            @if ($preview['user']->profile_photo_path)
                                                <img src="{{ asset('storage/' . $preview['user']->profile_photo_path) }}" alt="" class="size-10 rounded-full object-cover" />
                                            @else
                                                <div class="size-10 rounded-full bg-[#1392ec]/15 text-[#1392ec] flex items-center justify-center text-sm font-semibold">
                                                    {{ \Illuminate\Support\Str::of($preview['user']->name)->substr(0,1)->upper() }}
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ $preview['user']->name }}</p>
                                                <p class="text-xs text-slate-500 truncate">{{ $preview['user']->email }}</p>
                                            </div>
                                            <span @class([
                                                'text-xs font-medium uppercase tracking-wide',
                                                'text-emerald-500' => $preview['state'] === 'existing',
                                                'text-amber-500' => in_array($preview['state'], ['owner', 'already'], true),
                                            ])>
                                                @php
                                                    $previewLabel = match ($preview['state']) {
                                                        'existing' => __('invitations.preview.existing_user'),
                                                        'owner' => __('invitations.invited_owner'),
                                                        'already' => __('invitations.already_collaborator'),
                                                        default => '',
                                                    };
                                                @endphp
                                                {{ $previewLabel }}
                                            </span>
                                        @else
                                            <div class="size-10 rounded-full bg-slate-200 dark:bg-[#283239] flex items-center justify-center">
                                                <x-lucide-mail class="size-5 text-slate-500" />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ $inviteEmail }}</p>
                                                <p class="text-xs text-slate-500">{{ __('invitations.preview.new_invite') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Collaborators list --}}
                        <div class="mt-4 space-y-2">
                            @forelse ($this->collaborators as $collab)
                                <div wire:key="collab-{{ $collab->id }}"
                                    class="flex items-center justify-between rounded-lg border border-slate-200 bg-white p-2.5 dark:border-[#283239] dark:bg-[#1c2630]">
                                    <div class="flex items-center gap-3 min-w-0">
                                        @if ($collab->profile_photo_path)
                                            <img src="{{ asset('storage/' . $collab->profile_photo_path) }}" alt="" class="size-8 rounded-full object-cover" />
                                        @else
                                            <div class="size-8 rounded-full bg-[#1392ec]/15 text-[#1392ec] flex items-center justify-center text-xs font-semibold">
                                                {{ \Illuminate\Support\Str::of($collab->name)->substr(0,1)->upper() }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-sm text-slate-900 dark:text-white truncate">{{ $collab->name }}</p>
                                            <p class="text-xs text-slate-500 truncate">{{ $collab->email }}</p>
                                        </div>
                                    </div>
                                    @if ($this->isOwner)
                                        <button type="button" wire:click="removeCollaborator({{ $collab->id }})"
                                            wire:confirm="{{ __('invitations.remove') }}?"
                                            class="p-1.5 rounded text-slate-500 hover:bg-red-50 hover:text-red-500 dark:hover:bg-[#283239]"
                                            title="{{ __('invitations.remove') }}">
                                            <x-lucide-x class="size-4" />
                                        </button>
                                    @endif
                                </div>
                            @empty
                                @if (! $this->isOwner || $this->pendingInvitations->isEmpty())
                                    <p class="text-xs text-slate-500">{{ __('invitations.no_collaborators') }}</p>
                                @endif
                            @endforelse

                            {{-- Pending invitations --}}
                            @if ($this->isOwner && $this->pendingInvitations->isNotEmpty())
                                <p class="text-xs font-medium text-slate-500 mt-3">{{ __('invitations.pending_invitations') }}</p>
                                @foreach ($this->pendingInvitations as $inv)
                                    <div wire:key="inv-{{ $inv->id }}"
                                        class="flex items-center justify-between rounded-lg border border-dashed border-amber-500/40 bg-amber-500/5 p-2.5">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="size-8 rounded-full bg-amber-500/20 text-amber-500 flex items-center justify-center">
                                                <x-lucide-mail class="size-4" />
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm text-slate-900 dark:text-white truncate">{{ $inv->email }}</p>
                                                <p class="text-xs text-amber-500">{{ __('invitations.pending') }}</p>
                                            </div>
                                        </div>
                                        <button type="button" wire:click="cancelInvitation({{ $inv->id }})"
                                            class="p-1.5 rounded text-slate-500 hover:bg-red-50 hover:text-red-500 dark:hover:bg-[#283239]"
                                            title="{{ __('invitations.cancel_invitation') }}">
                                            <x-lucide-x class="size-4" />
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-slate-200 dark:border-[#283239]"></div>

                    {{-- File Attachments Section --}}
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <label class="text-sm font-medium text-slate-400">{{ __('app.attachments') }}</label>
                            <span class="text-xs text-slate-500">{{ __('app.file_count', ['count' => $this->attachments->count()]) }}</span>
                        </div>

                        {{-- File Dropzone --}}
                        <div x-on:dragover.prevent="dragging = true" x-on:dragleave.prevent="dragging = false"
                            x-on:drop.prevent="
                                dragging = false;
                                $wire.uploadMultiple('files', $event.dataTransfer.files);
                            "
                            :class="dragging ? 'border-[#1392ec] bg-[#1392ec]/5' : 'border-slate-200 dark:border-[#283239]'"
                            class="relative rounded-xl border-2 border-dashed p-6 text-center transition-all duration-200 hover:border-[#1392ec]/50 bg-slate-50 dark:bg-[#101a22]/50">
                            <input type="file" wire:model="files" multiple
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                            <div class="flex flex-col items-center">
                                <div class="p-3 rounded-full mb-3 bg-white dark:bg-[#1c2630]">
                                    <x-lucide-cloud-upload class="size-6 text-slate-400" />
                                </div>
                                <p class="text-sm text-slate-600 dark:text-slate-400 mb-1">
                                    <span class="text-[#1392ec] font-medium">{{ __('app.click_to_upload') }}</span>
                                    {{ __('app.or_drag_drop') }}
                                </p>
                                <p class="text-xs text-slate-500">{{ __('app.file_types_limit') }}</p>
                            </div>
                        </div>

                        {{-- Upload Progress --}}
                        <div wire:loading wire:target="files" class="mt-3">
                            <div class="flex items-center gap-2 text-sm text-slate-400">
                                <svg class="animate-spin size-4 text-[#1392ec]" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span>{{ __('app.uploading_files') }}</span>
                            </div>
                        </div>

                        {{-- Pending Files (before upload) --}}
                        @if (count($files) > 0)
                            <div class="mt-4 space-y-2">
                                <p class="text-xs text-slate-500 font-medium">{{ __('app.ready_to_upload') }}</p>
                                @foreach ($files as $index => $file)
                                    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-white p-3 dark:border-[#283239] dark:bg-[#1c2630]">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-amber-500/10 rounded-lg">
                                                <x-lucide-file class="size-4 text-amber-500" />
                                            </div>
                                            <div>
                                                <p class="text-sm text-white truncate max-w-[200px]">
                                                    {{ $file->getClientOriginalName() }}</p>
                                                <p class="text-xs text-slate-500">
                                                    {{ number_format($file->getSize() / 1024, 1) }} KB</p>
                                            </div>
                                        </div>
                                        <button wire:click="removeTempFile({{ $index }})"
                                            class="p-1 rounded text-slate-500 transition-colors hover:bg-slate-100 hover:text-red-400 dark:text-slate-400 dark:hover:bg-[#283239]">
                                            <x-lucide-x class="size-4" />
                                        </button>
                                    </div>
                                @endforeach
                                <button wire:click="uploadFiles" wire:loading.attr="disabled"
                                    class="w-full mt-2 px-4 py-2 bg-[#1392ec] hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50">
                                    {{ __('app.upload_files', ['count' => count($files)]) }}
                                </button>
                            </div>
                        @endif

                        {{-- Existing Attachments Grid --}}
                        @if ($this->attachments->count() > 0)
                            <div class="mt-4 grid grid-cols-2 gap-3">
                                @foreach ($this->attachments as $attachment)
                                    <div wire:key="attachment-{{ $attachment->id }}"
                                        class="group relative overflow-hidden rounded-lg border border-slate-200 bg-white transition-colors hover:border-[#1392ec]/50 dark:border-[#283239] dark:bg-[#1c2630]">
                                        @if ($attachment->isImage())
                                            {{-- Image Thumbnail --}}
                                            <a href="{{ $attachment->url }}" target="_blank"
                                                class="aspect-video">
                                                <img src="{{ $attachment->url }}" alt="{{ $attachment->file_name }}"
                                                    class="w-full h-full object-cover" />
                                            </a>
                                        @else
                                            {{-- File Type Icon --}}
                                            <a href="{{ $attachment->url }}" target="_blank"
                                                class="aspect-video flex items-center justify-center bg-slate-50 dark:bg-[#101a22]">
                                                @php
                                                    $ext = strtolower($attachment->extension);
                                                    $iconColor = match (true) {
                                                        in_array($ext, ['pdf']) => 'text-red-400',
                                                        in_array($ext, ['doc', 'docx']) => 'text-blue-400',
                                                        in_array($ext, ['xls', 'xlsx']) => 'text-emerald-400',
                                                        in_array($ext, ['zip', 'rar', '7z']) => 'text-amber-400',
                                                        default => 'text-slate-400',
                                                    };
                                                @endphp
                                                <div class="text-center">
                                                    <x-lucide-file-text
                                                        class="size-10 {{ $iconColor }} mx-auto mb-2" />
                                                    <span
                                                        class="text-xs font-bold uppercase {{ $iconColor }}">{{ $ext }}</span>
                                                </div>
                                            </a>
                                        @endif

                                        {{-- File Info --}}
                                        <div class="p-2">
                                            <p class="text-xs text-white truncate"
                                                title="{{ $attachment->file_name }}">
                                                {{ $attachment->file_name }}
                                            </p>
                                            <p class="text-[10px] text-slate-500">{{ $attachment->formatted_size }}
                                            </p>
                                        </div>

                                        {{-- Delete Button --}}
                                        <button wire:click="removeFile({{ $attachment->id }})"
                                            wire:confirm="{{ __('app.confirm_delete_file') }}"
                                            class="absolute top-2 right-2 p-1.5 bg-red-500/80 rounded-lg text-white opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600">
                                            <x-lucide-trash-2 class="size-3.5" />
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-[#283239] dark:bg-[#0d1419]">
                    <div>
                        @if ($this->isOwner)
                            <button wire:click="deleteTask" wire:confirm="{{ __('app.confirm_delete_task') }}"
                                wire:loading.attr="disabled" wire:target="deleteTask"
                                class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10 disabled:opacity-50 disabled:cursor-not-allowed">
                                <x-lucide-trash-2 class="size-4" />
                                <span wire:loading.remove wire:target="deleteTask">{{ __('app.delete_task') }}</span>
                                <span wire:loading wire:target="deleteTask">{{ __('app.deleting') }}</span>
                            </button>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <button wire:click="close"
                            class="px-4 py-2.5 text-sm font-medium text-slate-500 transition-colors hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">
                            {{ __('app.cancel') }}
                        </button>
                        <button wire:click="save" wire:loading.attr="disabled"
                            @disabled(! $this->canEditDescription)
                            class="px-6 py-2.5 bg-[#1392ec] hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <span wire:loading.remove wire:target="save">{{ __('app.save_changes') }}</span>
                            <span wire:loading wire:target="save">
                                <svg class="animate-spin size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
            @else
                {{-- Loading State --}}
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="animate-spin size-8 text-[#1392ec] mx-auto mb-3"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <p class="text-slate-400">{{ __('app.loading_task') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
