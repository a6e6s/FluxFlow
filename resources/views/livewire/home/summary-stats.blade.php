<div class="grid grid-cols-2 gap-4 md:grid-cols-5">
    @foreach ($stats as $stat)
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#283239] dark:bg-[#1c2630]">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $stat['label'] }}</p>
            <p class="mt-2 text-3xl font-bold {{ $stat['color'] }}">{{ $stat['value'] }}</p>
        </div>
    @endforeach
</div>
