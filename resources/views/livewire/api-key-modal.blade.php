<div
    x-data="{
        open: $wire.entangle('open'),
        copied: false,
        async copyKey(value) {
            if (! value) {
                return;
            }

            await navigator.clipboard.writeText(value);
            this.copied = true;

            setTimeout(() => {
                this.copied = false;
            }, 2000);
        }
    }"
    x-show="open"
    x-cloak
    @keydown.escape.window="if (open) $wire.close()"
    class="fixed inset-0 z-50 overflow-y-auto"
>
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
            class="relative w-full max-w-xl rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-[#283239] dark:bg-[#1c2630]"
        >
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-[#283239]">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-[#1392ec]/10 p-2">
                        <x-lucide-key class="size-5 text-[#1392ec]" />
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ __('API Key') }}</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-400">{{ __('Generate a key for external integrations and keep it private.') }}</p>
                    </div>
                </div>

                <button
                    @click="$wire.close()"
                    class="rounded-lg p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-[#283239] dark:hover:text-white"
                >
                    <x-lucide-x class="size-5" />
                </button>
            </div>

            <div class="space-y-5 p-6">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-[#283239] dark:bg-[#101a22]">
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-slate-900 dark:text-white">
                                {{ $hasApiKey ? __('Active API key') : __('No API key yet') }}
                            </p>
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                {{ $generatedAtLabel !== '' ? __('Last generated :time', ['time' => $generatedAtLabel]) : __('Create a key to authenticate requests from your external application.') }}
                            </p>
                        </div>

                        <span class="inline-flex items-center rounded-full border border-[#1392ec]/30 bg-[#1392ec]/10 px-3 py-1 text-xs font-medium text-[#6cc4ff]">
                            {{ __('Treat this like a password') }}
                        </span>
                    </div>

                    <div class="mt-4 space-y-2">
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">{{ __('API Key') }}</label>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <input
                                type="text"
                                readonly
                                wire:model="apiKey"
                                class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-white px-4 py-3 font-mono text-sm text-slate-900 placeholder:text-slate-500 focus:border-[#1392ec] focus:outline-none focus:ring-1 focus:ring-[#1392ec] dark:border-[#283239] dark:bg-[#0b1319] dark:text-white dark:placeholder:text-slate-600"
                                placeholder="{{ __('Generate a key to display it here') }}"
                            />

                            <button
                                type="button"
                                @click="copyKey($wire.apiKey)"
                                :disabled="! $wire.apiKey"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 disabled:cursor-not-allowed disabled:opacity-50 dark:border-[#283239] dark:text-slate-300 dark:hover:bg-[#283239] dark:hover:text-white"
                            >
                                <x-lucide-copy class="size-4" />
                                <span x-show="! copied">{{ __('Copy') }}</span>
                                <span x-show="copied">{{ __('Copied') }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        @click="$wire.close()"
                        class="px-4 py-2.5 text-sm font-medium text-slate-500 transition-colors hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                    >
                        {{ __('Close') }}
                    </button>

                    <button
                        type="button"
                        wire:click="generate"
                        wire:loading.attr="disabled"
                        wire:target="generate,regenerate"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-[#1392ec]/30 bg-[#1392ec]/10 px-5 py-2.5 text-sm font-medium text-[#1392ec] transition-colors hover:bg-[#1392ec]/20 disabled:opacity-50"
                    >
                        <x-lucide-wand-sparkles class="size-4" />
                        <span wire:loading.remove wire:target="generate">{{ __('Generate') }}</span>
                        <span wire:loading wire:target="generate">{{ __('Generating...') }}</span>
                    </button>

                    <button
                        type="button"
                        wire:click="regenerate"
                        wire:loading.attr="disabled"
                        wire:target="generate,regenerate"
                        @disabled(! $hasApiKey)
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#1392ec] px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-blue-500/20 transition-colors hover:bg-blue-600 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <x-lucide-refresh-cw class="size-4" />
                        <span wire:loading.remove wire:target="regenerate">{{ __('Regenerate') }}</span>
                        <span wire:loading wire:target="regenerate">{{ __('Regenerating...') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
