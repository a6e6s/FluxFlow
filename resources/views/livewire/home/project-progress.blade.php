<x-home-card :title="__('home.widgets.project_progress')" icon="folder-kanban">
    @forelse ($projects as $project)
        @php
            $percent = $project->tasks_count > 0 ? (int) round($project->done_tasks_count / $project->tasks_count * 100) : 0;
        @endphp

        <a href="{{ route('projects.show', $project->id) }}" wire:navigate class="block py-2.5">
            <div class="mb-1.5 flex items-center justify-between gap-3 text-sm">
                <span class="truncate font-medium text-slate-900 dark:text-white">{{ $project->icon }} {{ $project->title }}</span>
                <span class="shrink-0 text-xs font-semibold text-[#1392ec]">{{ __('home.labels.complete', ['percent' => $percent]) }}</span>
            </div>
            <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-[#283239]">
                <div class="h-full rounded-full bg-[#1392ec]" style="width: {{ $percent }}%"></div>
            </div>
        </a>
    @empty
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('home.empty') }}</p>
    @endforelse
</x-home-card>
