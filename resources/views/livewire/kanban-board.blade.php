<div
    x-data="kanbanBoard()"
    x-ref="kanbanRoot"
    class="flex-1 flex flex-col"
>
    @if($this->project)
        {{-- Project Header --}}
        <div class="px-8 py-6 flex items-end justify-between border-b border-slate-200 dark:border-[#283239]">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-semibold text-[#1392ec] uppercase tracking-wider">
                        {{ $this->project->priority->label() }} Priority
                    </span>
                    @if($this->project->archived_at)
                        <span class="text-xs text-slate-500">• Archived</span>
                    @endif
                </div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                    @if($this->project->icon)
                        <span>{{ $this->project->icon }}</span>
                    @endif
                    {{ $this->project->title }}
                </h1>
            </div>
            <div class="flex items-center gap-3">
                {{-- Archive Project Button --}}
                @unless($this->project->archived_at)
                    <button
                        wire:click="archiveProject"
                        wire:confirm="Are you sure you want to archive this project? It will be moved to the archived section."
                        class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-amber-500 dark:hover:text-amber-400 bg-slate-100 dark:bg-[#283239] rounded-lg hover:bg-amber-500/10 dark:hover:bg-amber-500/10 transition-colors"
                        title="Archive Project"
                    >
                        <x-lucide-archive class="size-4" />
                        Archive
                    </button>
                @endunless

                <button class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-[#283239] rounded-lg hover:bg-slate-200 dark:hover:bg-[#323d46] transition-colors">
                    <x-lucide-filter class="size-4" />
                    Filter
                </button>
                <button
                    wire:click="$dispatch('open-create-task-modal', { projectId: {{ $projectId }} })"
                    class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-white bg-[#1392ec] rounded-lg hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/20"
                >
                    <x-lucide-plus class="size-4" />
                    Add Task
                </button>
            </div>
        </div>

        {{-- Kanban Columns --}}
        <div class="flex-1 overflow-x-auto overflow-y-hidden p-8">
            <div class="flex h-full gap-6 min-w-[1200px]">
                {{-- To Do Column --}}
                @include('livewire.partials.kanban-column', [
                    'status' => 'todo',
                    'title' => 'To Do',
                    'tasks' => $this->todoTasks,
                    'dotColor' => 'bg-slate-400',
                    'emptyIcon' => 'circle',
                ])

                {{-- In Progress Column --}}
                @include('livewire.partials.kanban-column', [
                    'status' => 'doing',
                    'title' => 'In Progress',
                    'tasks' => $this->doingTasks,
                    'dotColor' => 'bg-[#1392ec] shadow-[0_0_8px_rgba(19,146,236,0.6)]',
                    'emptyIcon' => 'circle-dot',
                ])

                {{-- Review Column --}}
                @include('livewire.partials.kanban-column', [
                    'status' => 'review',
                    'title' => 'Review',
                    'tasks' => $this->reviewTasks,
                    'dotColor' => 'bg-amber-400',
                    'emptyIcon' => 'eye',
                ])

                {{-- Done Column --}}
                @include('livewire.partials.kanban-column', [
                    'status' => 'done',
                    'title' => 'Done',
                    'tasks' => $this->doneTasks,
                    'dotColor' => 'bg-emerald-500',
                    'emptyIcon' => 'circle-check',
                ])
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="flex-1 flex items-center justify-center">
            <div class="text-center">
                <div class="size-20 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-[#1c2630] flex items-center justify-center">
                    <x-lucide-columns-3 class="size-10 text-slate-400" />
                </div>
                <h2 class="text-xl font-semibold text-slate-700 dark:text-slate-200 mb-2">Select a Project</h2>
                <p class="text-slate-500 dark:text-slate-400 max-w-sm">
                    Choose a project from the sidebar to view its Kanban board, or create a new project to get started.
                </p>
            </div>
        </div>
    @endif
</div>

@script
<script>
    Alpine.data('kanbanBoard', () => ({
        draggedTaskId: null,
        sortableInstances: [],
        initialized: false,

        init() {
            const component = this;

            // Initialize sortable when component mounts
            this.waitForSortable(() => {
                this.initSortable();
            });

            // Re-initialize only when our specific component updates
            Livewire.hook('morph.updated', ({ el, component: liveComponent }) => {
                // Only reinit if the kanban board element itself was morphed
                if (el === component.$el) {
                    component.waitForSortable(() => {
                        setTimeout(() => component.initSortable(), 50);
                    });
                }
            });
        },

        waitForSortable(callback, attempts = 0) {
            if (typeof window.Sortable !== 'undefined') {
                callback();
            } else if (attempts < 50) {
                setTimeout(() => this.waitForSortable(callback, attempts + 1), 100);
            }
        },

        initSortable() {
            // Destroy existing instances first
            this.sortableInstances.forEach(instance => {
                if (instance && instance.destroy) {
                    instance.destroy();
                }
            });
            this.sortableInstances = [];

            const columns = this.$el.querySelectorAll('.kanban-column[data-column]');

            if (columns.length === 0) {
                return;
            }

            columns.forEach(column => {
                const instance = Sortable.create(column, {
                    group: 'kanban',
                    animation: 150,
                    ghostClass: 'opacity-30',
                    dragClass: 'shadow-glow',
                    chosenClass: 'task-chosen',
                    filter: '.empty-state',
                    draggable: '[data-task-id]',
                    forceFallback: true,
                    fallbackClass: 'sortable-fallback',
                    fallbackOnBody: true,

                    onStart: (evt) => {
                        this.draggedTaskId = parseInt(evt.item.dataset.taskId);
                        document.body.style.cursor = 'grabbing';
                        document.querySelectorAll('.empty-state').forEach(el => {
                            el.style.display = 'none';
                        });
                    },

                    onEnd: (evt) => {
                        document.body.style.cursor = '';
                        document.querySelectorAll('.empty-state').forEach(el => {
                            el.style.display = '';
                        });

                        const taskId = parseInt(evt.item.dataset.taskId);
                        const newStatus = evt.to.dataset.column;
                        const orderedIds = Array.from(evt.to.children)
                            .filter(el => el.dataset.taskId)
                            .map(el => parseInt(el.dataset.taskId));

                        $wire.moveTask(taskId, newStatus, orderedIds);
                        this.draggedTaskId = null;
                    }
                });

                this.sortableInstances.push(instance);
            });
        }
    }));
</script>
@endscript
