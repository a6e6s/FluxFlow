<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
    @foreach ($stats as $stat)
        <div class="relative flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#283239] dark:bg-[#1c2630]">
            <span class="absolute inset-x-0 top-0 h-px" style="background: linear-gradient(90deg, transparent, {{ $stat['accent'] }}, transparent)"></span>

            <div class="flex items-start justify-between gap-2">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl" style="background-color: {{ $stat['accent'] }}24; color: {{ $stat['accent'] }}">
                    <x-dynamic-component :component="'lucide-'.$stat['icon']" class="size-4" />
                </span>
                <x-home-pill :tone="$stat['tone']">{{ $stat['pill'] }}</x-home-pill>
            </div>

            <p class="mt-5 text-3xl font-bold text-slate-900 dark:text-white">{{ $stat['value'] }}</p>
            <p class="mt-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $stat['label'] }}</p>

            <div class="mt-4">
                <x-home-sparkline :values="$stat['series']" :color="$stat['accent']" />
            </div>
        </div>
    @endforeach
</div>
