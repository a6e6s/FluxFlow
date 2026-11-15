<div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#283239] dark:bg-[#1c2630]">
    <div class="mb-4 flex items-center gap-2">
        <x-dynamic-component :component="'lucide-'.$icon" class="size-4 text-[#1392ec]" />
        <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ $title }}</h2>
    </div>

    {{ $slot }}
</div>
