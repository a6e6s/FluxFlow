<x-home-card :title="__('home.widgets.project_progress')" icon="folder-kanban">
    @forelse ($projects as $project)
        @php
            $total = (int) $project->tasks_count;
            $done = (int) $project->done_tasks_count;
            $percent = $total > 0 ? (int) round($done / $total * 100) : 0;
            $statusTone = match ($project->progressStatus()) {
                'completed' => 'emerald',
                'at_risk' => 'rose',
                'in_progress' => 'primary',
                default => 'slate',
            };
        @endphp

        <a href="{{ route('projects.show', $project->id) }}" wire:navigate class="block py-2.5">
            <div class="mb-2 flex items-center justify-between gap-3">
                <span class="min-w-0 truncate text-sm font-medium text-slate-900 dark:text-white">{{ $project->icon }} {{ $project->title }}</span>
                <div class="flex shrink-0 items-center gap-2">
                    <x-home-pill :tone="$statusTone">{{ __('home.progress_status.'.$project->progressStatus()) }}</x-home-pill>
                    <span class="w-9 text-end text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $percent }}%</span>
                </div>
            </div>
            <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-[#283239]">
                <div class="h-full rounded-full" style="width: {{ $percent }}%; background-color: {{ $project->color ?? '#1392ec' }}"></div>
            </div>
        </a>
    @empty
        <x-home-empty icon="folder-kanban" :title="__('home.widgets.project_progress')">
            {{ __('home.empty') }}
        </x-home-empty>
    @endforelse
</x-home-card>
