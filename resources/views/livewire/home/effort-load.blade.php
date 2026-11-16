<x-home-card :title="__('home.widgets.effort_load')" icon="gauge" tone="amber">
    @forelse ($projects as $project)
        <a href="{{ route('projects.show', $project->id) }}" wire:navigate class="block py-2.5">
            <div class="mb-2 flex items-center justify-between gap-3 text-sm">
                <span class="truncate font-medium text-slate-900 dark:text-white">{{ $project->title }}</span>
                <span class="shrink-0 text-xs font-semibold text-slate-600 dark:text-slate-300">{{ __('home.labels.points', ['points' => $project->effort]) }}</span>
            </div>
            <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-[#283239]">
                <div class="h-full rounded-full bg-amber-400" style="width: {{ round($project->effort / $maxEffort * 100) }}%"></div>
            </div>
        </a>
    @empty
        <x-home-empty icon="gauge" :title="__('home.widgets.effort_load')">
            {{ __('home.states.effort_text') }}
        </x-home-empty>
    @endforelse
</x-home-card>
