<aside x-data="{
    init() {
            this.initSortable();
        },
        initSortable() {
            if (typeof Sortable === 'undefined') return;

            const el = this.$refs.projectList;
            if (!el) return;

            Sortable.create(el, {
                animation: 150,
                ghostClass: 'opacity-50',
                dragClass: 'shadow-glow',
                handle: '.drag-handle',
                onEnd: (evt) => {
                    const ids = Array.from(el.children).map(item => parseInt(item.dataset.projectId));
                    $wire.reorderProjects(ids);
                }
            });
        }
}" @class([
    'h-full flex flex-col shrink-0 overflow-hidden border-r border-slate-200 bg-white transition-all duration-300 dark:border-slate-700/50 dark:bg-slate-900',
    'w-20' => $collapsed,
    'w-80' => !$collapsed,
])>
    {{-- Header --}}
    <div class="flex items-start justify-between gap-2 p-4">
        @if (!$collapsed)
            <div class="min-w-0 flex flex-1 flex-col">
                <h1 class="text-sm font-semibold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                    {{ __('app.workspace') }}</h1>
                <div
                    class="mt-1 flex items-center gap-1 text-xs text-slate-500 transition-colors hover:text-primary cursor-pointer">
                    <span class="truncate">{{ auth()->user()->name }}</span>
                    <x-lucide-chevron-down class="size-3" />
                </div>
            </div>
        @endif

        <button type="button" wire:click="toggleSidebar"
            class="flex size-7 shrink-0 items-center justify-center rounded-md border border-slate-200 bg-slate-50 text-slate-500 shadow-sm transition-colors hover:border-slate-300 hover:bg-slate-100 hover:text-slate-900 dark:border-[#283239] dark:bg-[#1c2630] dark:text-slate-300 dark:hover:border-[#3a4650] dark:hover:bg-[#283239] dark:hover:text-white"
            aria-label="{{ $collapsed ? 'Expand sidebar' : 'Collapse sidebar' }}"
            title="{{ $collapsed ? 'Expand sidebar' : 'Collapse sidebar' }}">
            @if ($collapsed)
                <x-lucide-chevron-right class="size-[14px]" />
            @else
                <x-lucide-chevron-left class="size-[14px]" />
            @endif
        </button>
    </div>

    {{-- Projects List --}}
    <div @class([
        'flex-1 overflow-y-auto pb-4',
        'px-2' => $collapsed,
        'px-3' => !$collapsed,
    ])>
        <div class="space-y-6">
            {{-- Active Projects --}}
            <div>
                @if (!$collapsed)
                    <div class="mb-2 flex items-center justify-between px-3">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                            {{ __('app.active_projects') }}
                        </h3>
                        <button wire:click="$dispatch('open-create-project-modal')" type="button"
                            aria-label="{{ __('app.new_project') }}" title="{{ __('app.new_project') }}"
                            class="flex size-7 shrink-0 items-center justify-center rounded-md bg-primary text-white shadow-md shadow-blue-500/30 transition-all hover:bg-blue-600">
                            <x-lucide-plus class="size-4" />
                        </button>
                    </div>
                @endif
                <div x-ref="projectList" class="space-y-1" wire:ignore.self>
                    @forelse($this->ownedProjects as $project)
                        @php
                            $projectBorderColor = $project->color ?? '#3b82f6';
                            $projectBorderOpacity = $selectedProjectId === $project->id ? '20' : '40';
                        @endphp
                        <div data-project-id="{{ $project->id }}" wire:key="project-{{ $project->id }}"
                            @click="$wire.selectProject({{ $project->id }})" title="{{ $project->title }}"
                            aria-label="{{ $project->title }}"
                            style="border-color: {{ $projectBorderColor }}{{ $projectBorderOpacity }}"
                            @class([
                                'group relative flex cursor-pointer items-center rounded-lg border transition-all duration-200 border-slate-200 bg-slate-50 dark:border-slate-700/10 dark:bg-slate-800/80',
                                'justify-center p-2' => $collapsed,
                                'gap-3 p-3' => !$collapsed,
                                'bg-white shadow-md ring-1 ring-slate-200 dark:bg-slate-800/80 dark:ring-slate-700' =>
                                    $selectedProjectId === $project->id,
                                'hover:bg-slate-100 hover:border-slate-300 dark:hover:bg-slate-800/50' =>
                                    $selectedProjectId !== $project->id,
                                // High Priority Glow Effect
                                'ring-1 ring-red-500/20 shadow-[0_0_15px_rgba(239,68,68,0.12)]' =>
                                    $project->priority->value === 'high' &&
                                    $selectedProjectId !== $project->id,
                            ])>
                            {{-- Active Indicator --}}
                            @if ($selectedProjectId === $project->id)
                                <div class="absolute left-0 top-3 bottom-3 w-1 rounded-r-full"
                                    style="background-color: {{ $project->color ?? '#3b82f6' }}"></div>
                            @endif

                            @if (!$collapsed)
                                {{-- High Priority Indicator --}}
                                @if ($project->priority->value === 'high')
                                    <div
                                        class="absolute -top-1 -right-1 size-3 rounded-full border-2 border-white bg-red-500 animate-pulse dark:border-slate-900">
                                    </div>
                                @endif

                                {{-- Drag Handle --}}
                                <div
                                    class="drag-handle cursor-grab opacity-0 transition-opacity active:cursor-grabbing group-hover:opacity-100">
                                    <x-lucide-grip-vertical class="size-4 text-slate-400 dark:text-slate-500" />
                                </div>
                            @endif

                            {{-- Progress Ring --}}
                            <div class="relative flex size-10 shrink-0 items-center justify-center">
                                @php
                                    $total = $project->tasks_count ?? 0;
                                    $done = $project->done_tasks_count ?? 0;
                                    $percentage = $total > 0 ? round(($done / $total) * 100) : 0;
                                @endphp
                                <svg class="size-full -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-slate-300 dark:text-slate-700"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                        fill="none" stroke="currentColor" stroke-width="3" />
                                    <path style="color: {{ $project->color ?? '#3b82f6' }}"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                        fill="none" stroke="currentColor"
                                        stroke-dasharray="{{ $percentage }}, 100" stroke-width="3" />
                                </svg>
                                @if ($project->icon)
                                    <span
                                        class="absolute text-slate-600 dark:text-slate-300 text-lg">{{ $project->icon }}</span>
                                @else
                                    <x-lucide-folder class="absolute size-4 text-slate-500 dark:text-slate-400" />
                                @endif
                            </div>

                            @if (!$collapsed)
                                {{-- Project Info --}}
                                <div class="min-w-0 flex flex-1 flex-col">
                                    <span
                                        class="truncate text-sm font-medium text-slate-900 transition-colors group-hover:text-slate-950 dark:text-slate-200 dark:group-hover:text-white">
                                        {{ $project->title }}
                                    </span>
                                    <span class="text-xs transition-colors"
                                        style="color: {{ $project->color ?? '#3b82f6' }}">
                                        {{ $percentage }}% {{ __('app.complete') }}
                                    </span>
                                </div>

                                {{-- Actions Menu --}}
                                @if ($project->user_id === auth()->id())
                                <div x-data="{ open: false }" class="relative" @click.stop>
                                    <button @click="open = !open"
                                        class="rounded p-1 opacity-0 transition-all hover:bg-slate-200 group-hover:opacity-100 dark:hover:bg-slate-700">
                                        <x-lucide-more-vertical
                                            class="size-4 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white" />
                                    </button>
                                    <div x-show="open" x-transition @click.away="open = false"
                                        class="absolute rtl:left-0 ltr:right-0 top-full z-50 mt-1 w-40 rounded-lg border border-slate-200 bg-white py-1 shadow-xl dark:border-slate-700 dark:bg-slate-800">
                                        <button
                                            @click="open = false; $dispatch('open-edit-project-modal', { projectId: {{ $project->id }} })"
                                            class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white">
                                            <x-lucide-pencil class="size-4" />
                                            {{ __('app.edit') }}
                                        </button>
                                        <button wire:click="archiveProject({{ $project->id }})"
                                            class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white">
                                            <x-lucide-archive class="size-4" />
                                            {{ __('app.archive') }}
                                        </button>
                                    </div>
                                </div>
                                @endif
                            @endif
                        </div>
                    @empty
                        @if ($this->sharedProjects->isEmpty())
                            <div class="flex flex-col items-center justify-center py-8 text-slate-500">
                                <x-lucide-folder-plus class="size-10 mb-2 opacity-50" />
                                @if (!$collapsed)
                                    <span class="text-sm">{{ __('app.no_projects') }}</span>
                                    <button wire:click="$dispatch('open-create-project-modal')"
                                        class="mt-2 text-primary hover:underline">
                                        {{ __('app.create_first_project') }}
                                    </button>
                                @endif
                            </div>
                        @endif
                    @endforelse
                </div>
            </div>

            {{-- Shared Projects --}}
            @if ($this->sharedProjects->isNotEmpty())
                @if (!$collapsed)
                    <details class="group" @if ($this->sharedProjects->contains('id', $selectedProjectId)) open @endif>
                        <summary
                            class="flex cursor-pointer items-center justify-between px-3 py-2 text-xs font-semibold uppercase tracking-wider text-slate-600 transition-colors select-none hover:text-primary dark:text-slate-400">
                            <span>{{ __('app.shared_projects') }} ({{ $this->sharedProjects->count() }})</span>
                            <x-lucide-chevron-down class="size-4 transition-transform duration-200 group-open:rotate-180" />
                        </summary>
                        <div class="mt-1 space-y-1">
                            @foreach ($this->sharedProjects as $project)
                                @php
                                    $projectBorderColor = $project->color ?? '#3b82f6';
                                    $projectBorderOpacity = $selectedProjectId === $project->id ? '20' : '40';
                                @endphp
                                <div data-project-id="{{ $project->id }}" wire:key="shared-project-{{ $project->id }}"
                                    @click="$wire.selectProject({{ $project->id }})" title="{{ $project->title }}"
                                    aria-label="{{ $project->title }}"
                                    style="border-color: {{ $projectBorderColor }}{{ $projectBorderOpacity }}"
                                    @class([
                                        'group relative flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition-all duration-200 border-slate-200 bg-slate-50 dark:border-slate-700/10 dark:bg-slate-800/80',
                                        'bg-white shadow-md ring-1 ring-slate-200 dark:bg-slate-800/80 dark:ring-slate-700' =>
                                            $selectedProjectId === $project->id,
                                        'hover:bg-slate-100 hover:border-slate-300 dark:hover:bg-slate-800/50' =>
                                            $selectedProjectId !== $project->id,
                                        'ring-1 ring-red-500/20 shadow-[0_0_15px_rgba(239,68,68,0.12)]' =>
                                            $project->priority->value === 'high' &&
                                            $selectedProjectId !== $project->id,
                                    ])>
                                    @if ($selectedProjectId === $project->id)
                                        <div class="absolute left-0 top-3 bottom-3 w-1 rounded-r-full"
                                            style="background-color: {{ $project->color ?? '#3b82f6' }}"></div>
                                    @endif

                                    @if ($project->priority->value === 'high')
                                        <div
                                            class="absolute -top-1 -right-1 size-3 rounded-full border-2 border-white bg-red-500 animate-pulse dark:border-slate-900">
                                        </div>
                                    @endif

                                    <div class="relative flex size-10 shrink-0 items-center justify-center">
                                        @php
                                            $total = $project->tasks_count ?? 0;
                                            $done = $project->done_tasks_count ?? 0;
                                            $percentage = $total > 0 ? round(($done / $total) * 100) : 0;
                                        @endphp
                                        <svg class="size-full -rotate-90" viewBox="0 0 36 36">
                                            <path class="text-slate-300 dark:text-slate-700"
                                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                                fill="none" stroke="currentColor" stroke-width="3" />
                                            <path style="color: {{ $project->color ?? '#3b82f6' }}"
                                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                                fill="none" stroke="currentColor"
                                                stroke-dasharray="{{ $percentage }}, 100" stroke-width="3" />
                                        </svg>
                                        @if ($project->icon)
                                            <span
                                                class="absolute text-slate-600 dark:text-slate-300 text-lg">{{ $project->icon }}</span>
                                        @else
                                            <x-lucide-folder class="absolute size-4 text-slate-500 dark:text-slate-400" />
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex flex-1 flex-col">
                                        <span
                                            class="truncate text-sm font-medium text-slate-900 transition-colors group-hover:text-slate-950 dark:text-slate-200 dark:group-hover:text-white">
                                            {{ $project->title }}
                                        </span>
                                        <span class="text-xs transition-colors"
                                            style="color: {{ $project->color ?? '#3b82f6' }}">
                                            {{ $percentage }}% {{ __('app.complete') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </details>
                @else
                    <div class="space-y-1 pt-4">
                        @foreach ($this->sharedProjects as $project)
                            @php
                                $projectBorderColor = $project->color ?? '#3b82f6';
                                $projectBorderOpacity = $selectedProjectId === $project->id ? '20' : '40';
                            @endphp
                            <div data-project-id="{{ $project->id }}" wire:key="shared-project-collapsed-{{ $project->id }}"
                                @click="$wire.selectProject({{ $project->id }})" title="{{ $project->title }}"
                                aria-label="{{ $project->title }}"
                                style="border-color: {{ $projectBorderColor }}{{ $projectBorderOpacity }}"
                                @class([
                                    'group relative flex cursor-pointer items-center justify-center rounded-lg border p-2 transition-all duration-200 border-slate-200 bg-slate-50 dark:border-slate-700/10 dark:bg-slate-800/80',
                                    'bg-white shadow-md ring-1 ring-slate-200 dark:bg-slate-800/80 dark:ring-slate-700' =>
                                        $selectedProjectId === $project->id,
                                    'hover:bg-slate-100 hover:border-slate-300 dark:hover:bg-slate-800/50' =>
                                        $selectedProjectId !== $project->id,
                                    'ring-1 ring-red-500/20 shadow-[0_0_15px_rgba(239,68,68,0.12)]' =>
                                        $project->priority->value === 'high' &&
                                        $selectedProjectId !== $project->id,
                                ])>
                                @if ($selectedProjectId === $project->id)
                                    <div class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full"
                                        style="background-color: {{ $project->color ?? '#3b82f6' }}"></div>
                                @endif

                                <div class="relative flex size-10 shrink-0 items-center justify-center">
                                    @php
                                        $total = $project->tasks_count ?? 0;
                                        $done = $project->done_tasks_count ?? 0;
                                        $percentage = $total > 0 ? round(($done / $total) * 100) : 0;
                                    @endphp
                                    <svg class="size-full -rotate-90" viewBox="0 0 36 36">
                                        <path class="text-slate-300 dark:text-slate-700"
                                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                            fill="none" stroke="currentColor" stroke-width="3" />
                                        <path style="color: {{ $project->color ?? '#3b82f6' }}"
                                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                            fill="none" stroke="currentColor"
                                            stroke-dasharray="{{ $percentage }}, 100" stroke-width="3" />
                                    </svg>
                                    @if ($project->icon)
                                        <span class="absolute text-slate-600 dark:text-slate-300 text-lg">{{ $project->icon }}</span>
                                    @else
                                        <x-lucide-folder class="absolute size-4 text-slate-500 dark:text-slate-400" />
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

            {{-- Archived Projects (Collapsible) --}}
            @if (!$collapsed && $this->archivedProjects->isNotEmpty())
                <details class="group">
                    <summary
                        class="flex cursor-pointer items-center justify-between px-3 py-2 text-xs font-semibold uppercase tracking-wider text-slate-600 transition-colors select-none hover:text-primary dark:text-slate-400">
                        <span>{{ __('app.archived_projects') }} ({{ $this->archivedProjects->count() }})</span>
                        <x-lucide-chevron-down class="size-4 transition-transform duration-200 group-open:rotate-180" />
                    </summary>
                    <div class="space-y-1 mt-1">
                        @foreach ($this->archivedProjects as $project)
                            <div wire:key="archived-{{ $project->id }}" @class([
                                'group flex cursor-pointer items-center gap-3 rounded-lg p-3 opacity-60 transition-all hover:opacity-100',
                                'hover:bg-slate-50 dark:hover:bg-slate-800/50',
                            ])>
                                {{-- Progress Ring (Grayscale) --}}
                                <div class="relative flex size-10 shrink-0 items-center justify-center grayscale">
                                    @php
                                        $total = $project->tasks_count ?? 0;
                                        $done = $project->done_tasks_count ?? 0;
                                        $percentage = $total > 0 ? round(($done / $total) * 100) : 0;
                                    @endphp
                                    <svg class="size-full -rotate-90" viewBox="0 0 36 36">
                                        <path class="text-slate-300 dark:text-slate-700"
                                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                            fill="none" stroke="currentColor" stroke-width="3" />
                                        <path class="text-slate-400 dark:text-slate-500"
                                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                            fill="none" stroke="currentColor"
                                            stroke-dasharray="{{ $percentage }}, 100" stroke-width="3" />
                                    </svg>
                                    <x-lucide-archive class="absolute size-4 text-slate-400 dark:text-slate-500" />
                                </div>

                                {{-- Project Info --}}
                                <div class="min-w-0 flex flex-1 flex-col">
                                    <span class="truncate text-sm font-medium text-slate-600 dark:text-slate-400">
                                        {{ $project->title }}
                                    </span>
                                    <span class="text-xs text-slate-500">
                                        Archived {{ $project->archived_at->format('M d') }}
                                    </span>
                                </div>

                                {{-- Restore Button --}}
                                <button wire:click.stop="restoreProject({{ $project->id }})"
                                    class="rounded p-1 opacity-0 transition-all hover:bg-slate-200 group-hover:opacity-100 dark:hover:bg-slate-700"
                                    title="Restore project">
                                    <x-lucide-archive-restore
                                        class="size-4 text-slate-500 dark:text-slate-400 hover:text-primary" />
                                </button>
                            </div>
                        @endforeach
                    </div>
                </details>
            @endif
        </div>
    </div>

    {{-- Footer --}}
    <div class="border-t border-slate-200 p-4 dark:border-slate-700/50 ">
        @if ($collapsed)
            <button wire:click="$dispatch('open-create-project-modal')" type="button"
                class="mx-auto flex size-8 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-100 hover:text-primary dark:hover:bg-slate-800/50"
                aria-label="{{ __('app.new_project') }}" title="{{ __('app.new_project') }}">
                <x-lucide-plus-circle class="size-4" />
            </button>
        @else
            <button wire:click="$dispatch('open-create-project-modal')" type="button"
                class="flex w-full cursor-pointer items-center gap-3 rounded-lg p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-primary dark:hover:bg-slate-800/50">
                <x-lucide-plus-circle class="size-5" />
                <span class="text-sm font-medium">{{ __('app.new_project') }}</span>
            </button>
        @endif
    </div>
</aside>
