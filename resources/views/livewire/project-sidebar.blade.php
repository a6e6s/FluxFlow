<aside
    x-data="{
        showArchived: false,
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
    }"
    class="w-80 flex flex-col border-r border-slate-700/50 bg-slate-900 shrink-0 transition-all duration-300"
>
    {{-- Header --}}
    <div class="p-5 flex items-center justify-between">
        <div class="flex flex-col">
            <h1 class="text-sm font-semibold text-slate-200 uppercase tracking-wider">Workspace</h1>
            <div class="flex items-center gap-1 text-xs text-slate-500 mt-1 cursor-pointer hover:text-primary transition-colors">
                <span>{{ auth()->user()->name }}</span>
                <x-lucide-chevron-down class="size-3" />
            </div>
        </div>
        <button
            wire:click="$dispatch('open-create-project-modal')"
            class="size-8 flex items-center justify-center rounded-lg bg-primary hover:bg-blue-600 text-white shadow-lg shadow-blue-500/30 transition-all"
        >
            <x-lucide-plus class="size-5" />
        </button>
    </div>

    {{-- Projects List --}}
    <div class="flex-1 overflow-y-auto px-3 pb-4">
        <div class="space-y-6">
            {{-- Active Projects --}}
            <div>
                <h3 class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                    Active Projects
                </h3>
                <div x-ref="projectList" class="space-y-1" wire:ignore.self>
                    @forelse($this->projects as $project)
                        <div
                            data-project-id="{{ $project->id }}"
                            wire:key="project-{{ $project->id }}"
                            @click="$wire.selectProject({{ $project->id }})"
                            @class([
                                'group relative flex items-center gap-3 p-3 rounded-lg cursor-pointer transition-all duration-200',
                                'bg-slate-800/80 border border-slate-700/50 shadow-glow' => $selectedProjectId === $project->id,
                                'hover:bg-slate-800/50' => $selectedProjectId !== $project->id,
                            ])
                        >
                            {{-- Active Indicator --}}
                            @if($selectedProjectId === $project->id)
                                <div
                                    class="absolute left-0 top-3 bottom-3 w-1 rounded-r-full"
                                    style="background-color: {{ $project->color ?? '#3b82f6' }}"
                                ></div>
                            @endif

                            {{-- Drag Handle --}}
                            <div class="drag-handle cursor-grab active:cursor-grabbing opacity-0 group-hover:opacity-100 transition-opacity">
                                <x-lucide-grip-vertical class="size-4 text-slate-500" />
                            </div>

                            {{-- Progress Ring --}}
                            <div class="relative size-10 shrink-0 flex items-center justify-center">
                                @php
                                    $total = $project->tasks_count ?? 0;
                                    $done = $project->done_tasks_count ?? 0;
                                    $percentage = $total > 0 ? round(($done / $total) * 100) : 0;
                                @endphp
                                <svg class="size-full -rotate-90" viewBox="0 0 36 36">
                                    <path
                                        class="text-slate-700"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="3"
                                    />
                                    <path
                                        style="color: {{ $project->color ?? '#3b82f6' }}"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-dasharray="{{ $percentage }}, 100"
                                        stroke-width="3"
                                    />
                                </svg>
                                @if($project->icon)
                                    <span class="absolute text-slate-300 text-lg">{{ $project->icon }}</span>
                                @else
                                    <x-lucide-folder class="absolute size-4 text-slate-400" />
                                @endif
                            </div>

                            {{-- Project Info --}}
                            <div class="flex flex-col flex-1 min-w-0">
                                <span class="text-sm font-medium text-slate-200 truncate group-hover:text-white transition-colors">
                                    {{ $project->title }}
                                </span>
                                <span
                                    class="text-xs transition-colors"
                                    style="color: {{ $project->color ?? '#3b82f6' }}"
                                >
                                    {{ $percentage }}% Complete
                                </span>
                            </div>

                            {{-- Actions Menu --}}
                            <div
                                x-data="{ open: false }"
                                class="relative"
                                @click.stop
                            >
                                <button
                                    @click="open = !open"
                                    class="opacity-0 group-hover:opacity-100 p-1 rounded hover:bg-slate-700 transition-all"
                                >
                                    <x-lucide-more-vertical class="size-4 text-slate-400 hover:text-white" />
                                </button>
                                <div
                                    x-show="open"
                                    x-transition
                                    @click.away="open = false"
                                    class="absolute right-0 top-full mt-1 w-40 bg-slate-800 border border-slate-700 rounded-lg shadow-xl z-50 py-1"
                                >
                                    <button
                                        wire:click="archiveProject({{ $project->id }})"
                                        class="w-full px-3 py-2 text-left text-sm text-slate-300 hover:bg-slate-700 hover:text-white flex items-center gap-2"
                                    >
                                        <x-lucide-archive class="size-4" />
                                        Archive
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 text-slate-500">
                            <x-lucide-folder-plus class="size-10 mb-2 opacity-50" />
                            <span class="text-sm">No projects yet</span>
                            <button
                                wire:click="$dispatch('open-create-project-modal')"
                                class="mt-2 text-xs text-primary hover:underline"
                            >
                                Create your first project
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Archived Projects (Collapsible) --}}
            @if($this->archivedProjects->isNotEmpty())
                <details class="group">
                    <summary class="flex items-center justify-between px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider cursor-pointer hover:text-primary transition-colors select-none">
                        <span>Archived Projects ({{ $this->archivedProjects->count() }})</span>
                        <x-lucide-chevron-down class="size-4 transition-transform duration-200 group-open:rotate-180" />
                    </summary>
                    <div class="space-y-1 mt-1">
                        @foreach($this->archivedProjects as $project)
                            <div
                                wire:key="archived-{{ $project->id }}"
                                @class([
                                    'group flex items-center gap-3 p-3 rounded-lg cursor-pointer opacity-60 hover:opacity-100 transition-all',
                                    'hover:bg-slate-800/50',
                                ])
                            >
                                {{-- Progress Ring (Grayscale) --}}
                                <div class="relative size-10 shrink-0 flex items-center justify-center grayscale">
                                    @php
                                        $total = $project->tasks_count ?? 0;
                                        $done = $project->done_tasks_count ?? 0;
                                        $percentage = $total > 0 ? round(($done / $total) * 100) : 0;
                                    @endphp
                                    <svg class="size-full -rotate-90" viewBox="0 0 36 36">
                                        <path
                                            class="text-slate-700"
                                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="3"
                                        />
                                        <path
                                            class="text-slate-500"
                                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-dasharray="{{ $percentage }}, 100"
                                            stroke-width="3"
                                        />
                                    </svg>
                                    <x-lucide-archive class="absolute size-4 text-slate-500" />
                                </div>

                                {{-- Project Info --}}
                                <div class="flex flex-col flex-1 min-w-0">
                                    <span class="text-sm font-medium text-slate-400 truncate">
                                        {{ $project->title }}
                                    </span>
                                    <span class="text-xs text-slate-500">
                                        Archived {{ $project->archived_at->format('M d') }}
                                    </span>
                                </div>

                                {{-- Restore Button --}}
                                <button
                                    wire:click.stop="restoreProject({{ $project->id }})"
                                    class="opacity-0 group-hover:opacity-100 p-1 rounded hover:bg-slate-700 transition-all"
                                    title="Restore project"
                                >
                                    <x-lucide-archive-restore class="size-4 text-slate-400 hover:text-primary" />
                                </button>
                            </div>
                        @endforeach
                    </div>
                </details>
            @endif
        </div>
    </div>

    {{-- Footer --}}
    <div class="p-4 border-t border-slate-700/50">
        <button
            wire:click="$dispatch('open-create-project-modal')"
            class="flex items-center gap-3 text-slate-500 hover:text-primary cursor-pointer transition-colors p-2 rounded-lg hover:bg-slate-800/50 w-full"
        >
            <x-lucide-plus-circle class="size-5" />
            <span class="text-sm font-medium">New Project</span>
        </button>
    </div>
</aside>
