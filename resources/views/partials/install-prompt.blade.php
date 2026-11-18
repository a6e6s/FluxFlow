<div
    x-data="{
        deferredPrompt: null,
        dismissed: localStorage.getItem('fluxflow.install-dismissed') === '1',
        async install() {
            const prompt = this.deferredPrompt;
            this.deferredPrompt = null;
            prompt.prompt();
            await prompt.userChoice;
        },
        dismiss() {
            this.dismissed = true;
            localStorage.setItem('fluxflow.install-dismissed', '1');
        },
    }"
    x-on:beforeinstallprompt.window="$event.preventDefault(); deferredPrompt = $event"
    x-on:appinstalled.window="deferredPrompt = null"
    x-show="deferredPrompt && !dismissed"
    role="dialog"
    aria-labelledby="install-prompt-title"
    class="fixed inset-x-3 bottom-3 z-50 flex items-center gap-3 rounded-xl border border-zinc-200 bg-white p-4 shadow-lg md:inset-x-auto md:end-4 md:w-96 dark:border-zinc-700 dark:bg-zinc-900"
>
    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-sky-600/10 text-sky-600 dark:text-sky-400">
        <x-lucide-download class="size-5" />
    </div>

    <div class="min-w-0 flex-1">
        <p id="install-prompt-title" class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('app.install_title') }}</p>
        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('app.install_description') }}</p>
    </div>

    <div class="flex shrink-0 items-center gap-1">
        <button
            type="button"
            x-on:click="dismiss()"
            class="min-h-11 rounded-lg px-3 text-sm font-medium text-zinc-500 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800"
        >{{ __('app.install_dismiss') }}</button>
        <button
            type="button"
            x-on:click="install()"
            class="min-h-11 rounded-lg bg-sky-600 px-3 text-sm font-semibold text-white hover:bg-sky-700"
        >{{ __('app.install_action') }}</button>
    </div>
</div>
