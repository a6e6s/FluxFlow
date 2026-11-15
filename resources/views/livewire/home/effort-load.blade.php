<x-home-card :title="__('home.widgets.effort_load')" icon="gauge">
    @forelse ($projects as $project)
        <a href="{{ route('projects.show', $project->id) }}" wire:navigate class="block py-2.5">
            <div class="mb-1.5 flex items-center justify-between gap-3 text-sm">
                <span class="truncate font-medium text-slate-900 dark:text-white">{{ $project->title }}</span>
                <span class="shrink-0 text-xs text-slate-500 dark:text-slate-400">{{ __('home.labels.points', ['points' => $project->effort]) }}</span>
            </div>
            <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-[#283239]">
                <div class="h-full rounded-full bg-amber-400" style="width: {{ round($project->effort / $maxEffort * 100) }}%"></div>
            </div>
        </a>
    @empty
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('home.empty') }}</p>
    @endforelse
</x-home-card>
