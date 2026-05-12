<div x-data="{ open: @entangle('open') }" @keydown.escape.window="open = false">
    {{-- Bell trigger --}}
    <button type="button" wire:click="toggle"
        class="flex items-center justify-center size-9 rounded-lg hover:bg-slate-100 dark:hover:bg-[#283239] text-slate-500 dark:text-slate-400 transition-colors relative"
        :title="'{{ __('invitations.drawer.title') }}'">
        <x-lucide-bell class="size-5" />

        @if ($this->unreadCount > 0)
            <span
                class="absolute -top-1 -end-1 min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] font-semibold flex items-center justify-center border-2 border-white dark:border-[#111518]">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    {{-- Backdrop --}}
    <div x-show="open" x-transition.opacity wire:click="close"
        class="fixed inset-0 bg-slate-900/40 dark:bg-black/60 z-40" style="display: none;"></div>

    {{-- Drawer panel --}}
    <aside x-show="open" x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full rtl:-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full rtl:-translate-x-full"
        class="fixed top-0 end-0 h-full w-full sm:w-[420px] max-w-full bg-white dark:bg-[#111518] border-s border-slate-200 dark:border-[#283239] shadow-2xl z-50 flex flex-col"
        style="display: none;"
        role="dialog" aria-modal="true" aria-labelledby="notification-drawer-title">

        {{-- Header --}}
        <header
            class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-[#283239] shrink-0">
            <div class="flex items-center gap-2">
                <x-lucide-bell class="size-5 text-[#1392ec]" />
                <h2 id="notification-drawer-title" class="text-base font-semibold text-slate-900 dark:text-white">
                    {{ __('invitations.drawer.title') }}
                </h2>
                @if ($this->unreadCount > 0)
                    <span
                        class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-[#1392ec]/10 text-[#1392ec] text-xs font-semibold">
                        {{ $this->unreadCount }}
                    </span>
                @endif
            </div>
            <button type="button" wire:click="close"
                class="flex items-center justify-center size-8 rounded-lg hover:bg-slate-100 dark:hover:bg-[#283239] text-slate-500 dark:text-slate-400 transition-colors"
                aria-label="{{ __('app.close') ?? 'Close' }}">
                <x-lucide-x class="size-4" />
            </button>
        </header>

        {{-- Content --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            @forelse ($this->pendingInvitations as $invitation)
                @php
                    $task = $invitation->task;
                    $project = $task?->project;
                    $inviter = $invitation->inviter;
                @endphp
                <article wire:key="invite-{{ $invitation->id }}"
                    class="rounded-xl border border-slate-200 dark:border-[#283239] bg-slate-50 dark:bg-[#1c2630] p-4">
                    <div class="flex items-start gap-3">
                        <div
                            class="size-10 shrink-0 flex items-center justify-center rounded-full bg-[#1392ec]/15 text-[#1392ec] font-semibold">
                            {{ $inviter ? mb_substr($inviter->name, 0, 1) : '?' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-slate-900 dark:text-white">
                                <span class="font-semibold">{{ $inviter?->name ?? __('invitations.drawer.unknown_sender') }}</span>
                                <span class="text-slate-500 dark:text-slate-400">
                                    {{ __('invitations.drawer.invited_you') }}
                                </span>
                            </p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                <x-lucide-folder class="inline-block size-3 -mt-0.5" />
                                {{ $project?->title ?? __('invitations.drawer.unknown_project') }}
                                <span class="mx-1">·</span>
                                <x-lucide-square-check-big class="inline-block size-3 -mt-0.5" />
                                {{ $task?->title ?? __('invitations.drawer.unknown_task') }}
                            </p>
                            <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">
                                {{ __('invitations.notification.body', [
                                    'inviter' => $inviter?->name ?? '',
                                    'task' => $task?->title ?? '',
                                ]) }}
                            </p>
                            <p class="mt-2 text-xs text-slate-400">
                                {{ $invitation->created_at?->diffForHumans() }}
                            </p>

                            <div class="mt-3 flex items-center gap-2">
                                <button type="button" wire:click="accept({{ $invitation->id }})"
                                    wire:loading.attr="disabled" wire:target="accept({{ $invitation->id }}),decline({{ $invitation->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#1392ec] hover:bg-[#0f7bc6] text-white text-xs font-medium px-3 py-1.5 transition-colors disabled:opacity-50">
                                    <x-lucide-check class="size-3.5" />
                                    {{ __('invitations.drawer.accept') }}
                                </button>
                                <button type="button" wire:click="decline({{ $invitation->id }})"
                                    wire:loading.attr="disabled" wire:target="accept({{ $invitation->id }}),decline({{ $invitation->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 dark:border-[#283239] hover:bg-slate-100 dark:hover:bg-[#283239] text-slate-700 dark:text-slate-300 text-xs font-medium px-3 py-1.5 transition-colors disabled:opacity-50">
                                    <x-lucide-x class="size-3.5" />
                                    {{ __('invitations.drawer.decline') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="flex flex-col items-center justify-center text-center py-16 gap-3">
                    <div
                        class="size-14 rounded-full bg-slate-100 dark:bg-[#1c2630] flex items-center justify-center">
                        <x-lucide-bell-off class="size-6 text-slate-400" />
                    </div>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                        {{ __('invitations.drawer.empty_title') }}
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-[260px]">
                        {{ __('invitations.drawer.empty_subtitle') }}
                    </p>
                </div>
            @endforelse
        </div>
    </aside>
</div>
