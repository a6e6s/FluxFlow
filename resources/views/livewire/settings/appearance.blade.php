<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading=" __('Update the appearance settings for your account')">
        <div
            x-data="{ appearance: window.FluxFlowTheme.getAppearance(), dark: window.FluxFlowTheme.isDark() }"
            x-init="window.addEventListener('flux-theme-changed', (event) => { appearance = event.detail.appearance; dark = event.detail.dark })"
            class="grid gap-3 sm:grid-cols-3"
        >
            <button
                type="button"
                x-on:click="window.FluxFlowTheme.set('light')"
                :class="appearance === 'light' ? 'border-blue-500 bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-200' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-[#111518] dark:text-slate-300 dark:hover:border-slate-600 dark:hover:bg-[#1b2024]'"
                class="flex items-center gap-3 rounded-xl border px-4 py-3 text-sm font-medium transition-colors"
            >
                <x-lucide-sun class="size-4" />
                <span>{{ __('Light') }}</span>
            </button>

            <button
                type="button"
                x-on:click="window.FluxFlowTheme.set('dark')"
                :class="appearance === 'dark' ? 'border-blue-500 bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-200' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-[#111518] dark:text-slate-300 dark:hover:border-slate-600 dark:hover:bg-[#1b2024]'"
                class="flex items-center gap-3 rounded-xl border px-4 py-3 text-sm font-medium transition-colors"
            >
                <x-lucide-moon class="size-4" />
                <span>{{ __('Dark') }}</span>
            </button>

            <button
                type="button"
                x-on:click="window.FluxFlowTheme.set('system')"
                :class="appearance === 'system' ? 'border-blue-500 bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-200' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-[#111518] dark:text-slate-300 dark:hover:border-slate-600 dark:hover:bg-[#1b2024]'"
                class="flex items-center gap-3 rounded-xl border px-4 py-3 text-sm font-medium transition-colors"
            >
                <x-lucide-monitor class="size-4" />
                <span>{{ __('System') }}</span>
            </button>
        </div>
    </x-settings.layout>
</section>
