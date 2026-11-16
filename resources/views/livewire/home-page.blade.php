<div class="flex-1 overflow-y-auto">
    <div class="space-y-6 p-6 lg:p-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-[#1392ec]">{{ now()->translatedFormat('l, F j, Y') }}</p>
                <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ __('home.title') }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('home.greeting', ['name' => auth()->user()->name]) }}</p>
            </div>

            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <p class="mb-1.5 text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('home.period.label') }}</p>
                    <label class="relative flex items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm shadow-sm transition-colors hover:border-slate-300 focus-within:border-[#1392ec] dark:border-[#283239] dark:bg-[#1c2630] dark:hover:border-[#3a4650]">
                        <x-lucide-calendar class="size-4 shrink-0 text-slate-400" />
                        <select id="home-period" wire:model.live="period" aria-label="{{ __('home.period.label') }}"
                            class="appearance-none bg-transparent pe-6 font-medium text-slate-900 focus:outline-none dark:text-white dark:[color-scheme:dark]">
                            <option value="this_month" class="bg-white text-slate-900 dark:bg-[#1c2630] dark:text-white">{{ __('home.period.this_month') }}</option>
                            <option value="last_month" class="bg-white text-slate-900 dark:bg-[#1c2630] dark:text-white">{{ __('home.period.last_month') }}</option>
                            <option value="custom" class="bg-white text-slate-900 dark:bg-[#1c2630] dark:text-white">{{ __('home.period.custom') }}</option>
                        </select>
                        <x-lucide-chevron-down class="pointer-events-none absolute end-3 size-4 text-slate-400" />
                    </label>
                </div>

                @if ($period === 'custom')
                    <div>
                        <label for="home-from" class="mb-1.5 block text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('home.period.from') }}</label>
                        <input id="home-from" type="date" wire:model.live.debounce.300ms="from"
                            class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm focus:border-[#1392ec] focus:outline-none dark:border-[#283239] dark:bg-[#1c2630] dark:text-white dark:[color-scheme:dark]" />
                    </div>
                    <div>
                        <label for="home-to" class="mb-1.5 block text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('home.period.to') }}</label>
                        <input id="home-to" type="date" wire:model.live.debounce.300ms="to"
                            class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm focus:border-[#1392ec] focus:outline-none dark:border-[#283239] dark:bg-[#1c2630] dark:text-white dark:[color-scheme:dark]" />
                    </div>
                @endif

                <span class="inline-flex items-center gap-2 rounded-xl border border-[#1392ec]/50 bg-[#1392ec]/10 px-4 py-2.5 text-sm font-semibold text-[#1392ec] shadow-sm">
                    <span class="size-2 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]"></span>
                    {{ $this->range['label'] }}
                </span>
            </div>
        </div>

        <livewire:home.summary-stats lazy :from="$this->range['start']" :to="$this->range['end']" />

        {{-- Activity spans two rows on wide screens, so the grid places the overdue list beside it --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="h-full lg:col-span-2 [&>div]:h-full">
                <livewire:home.due-soon lazy />
            </div>

            <div class="h-full lg:row-span-2 [&>div]:h-full">
                <livewire:home.recent-activity lazy />
            </div>

            <div class="h-full [&>div]:h-full">
                <livewire:home.project-progress lazy />
            </div>

            <div class="h-full [&>div]:h-full">
                <livewire:home.effort-load lazy :from="$this->range['start']" :to="$this->range['end']" />
            </div>

            <div class="h-full [&>div]:h-full">
                <livewire:home.upcoming-deadlines lazy />
            </div>

            <div class="h-full [&>div]:h-full">
                <livewire:home.my-tasks lazy />
            </div>

            <div class="h-full [&>div]:h-full">
                <livewire:home.pending-invitations lazy />
            </div>
        </div>
    </div>
</div>
