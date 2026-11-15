<div class="flex-1 overflow-y-auto">
    <div class="mx-auto max-w-7xl space-y-6 p-6 lg:p-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ __('home.title') }}</h1>
                <p class="mt-1 text-slate-500 dark:text-slate-400">{{ __('home.greeting', ['name' => auth()->user()->name]) }}</p>
            </div>

            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="home-period" class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('home.period.label') }}</label>
                    <select id="home-period" wire:model.live="period"
                        class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#1392ec] focus:outline-none dark:border-[#283239] dark:bg-[#1c2630] dark:text-white">
                        <option value="this_month">{{ __('home.period.this_month') }}</option>
                        <option value="last_month">{{ __('home.period.last_month') }}</option>
                        <option value="custom">{{ __('home.period.custom') }}</option>
                    </select>
                </div>

                @if ($period === 'custom')
                    <div>
                        <label for="home-from" class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('home.period.from') }}</label>
                        <input id="home-from" type="date" wire:model.live.debounce.300ms="from"
                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#1392ec] focus:outline-none dark:border-[#283239] dark:bg-[#1c2630] dark:text-white" />
                    </div>
                    <div>
                        <label for="home-to" class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('home.period.to') }}</label>
                        <input id="home-to" type="date" wire:model.live.debounce.300ms="to"
                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-[#1392ec] focus:outline-none dark:border-[#283239] dark:bg-[#1c2630] dark:text-white" />
                    </div>
                @endif

                <p class="pb-2 text-sm font-semibold text-[#1392ec]">{{ $this->range['label'] }}</p>
            </div>
        </div>

        <livewire:home.summary-stats lazy :from="$this->range['start']" :to="$this->range['end']" />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 xl:grid-cols-3">
            <livewire:home.due-soon lazy />
            <livewire:home.upcoming-deadlines lazy />
            <livewire:home.my-tasks lazy />
            <livewire:home.project-progress lazy />
            <livewire:home.effort-load lazy :from="$this->range['start']" :to="$this->range['end']" />
            <livewire:home.recent-activity lazy />
            <livewire:home.pending-invitations lazy />
        </div>
    </div>
</div>
