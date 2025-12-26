<div class="flex-1 flex flex-col">
    @if($projectId)
        <div class="px-8 py-6 flex items-end justify-between border-b border-slate-200 dark:border-slate-700/50">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-semibold text-[#1392ec] uppercase tracking-wider">Project</span>
                </div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                    Project #{{ $projectId }}
                </h1>
            </div>
            <div class="flex items-center gap-3">
                <button class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-[#283239] rounded-lg hover:bg-slate-200 dark:hover:bg-[#323d46] transition-colors">
                    <x-lucide-filter class="size-4" />
                    Filter
                </button>
                <button class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-white bg-[#1392ec] rounded-lg hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/20">
                    <x-lucide-plus class="size-4" />
                    Add Task
                </button>
            </div>
        </div>
        <div class="flex-1 p-8">
            {{-- Kanban columns will go here --}}
            <div class="flex gap-6 h-full">
                {{-- To Do Column --}}
                <div class="flex flex-col w-80 shrink-0">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <div class="flex items-center gap-2">
                            <div class="size-2 rounded-full bg-slate-400"></div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">To Do</h3>
                            <span class="px-2 py-0.5 rounded-full bg-slate-200 dark:bg-[#283239] text-xs font-medium text-slate-600 dark:text-slate-400">0</span>
                        </div>
                    </div>
                    <div class="flex-1 flex flex-col items-center justify-center p-4 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-[#101a22]/50">
                        <div class="p-3 bg-slate-100 dark:bg-[#1c2630] rounded-full mb-3">
                            <x-lucide-inbox class="size-5 text-slate-400" />
                        </div>
                        <span class="text-sm text-slate-500 font-medium">No tasks</span>
                    </div>
                </div>

                {{-- In Progress Column --}}
                <div class="flex flex-col w-80 shrink-0">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <div class="flex items-center gap-2">
                            <div class="size-2 rounded-full bg-[#1392ec] shadow-[0_0_8px_rgba(19,146,236,0.6)]"></div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">In Progress</h3>
                            <span class="px-2 py-0.5 rounded-full bg-slate-200 dark:bg-[#283239] text-xs font-medium text-slate-600 dark:text-slate-400">0</span>
                        </div>
                    </div>
                    <div class="flex-1 flex flex-col items-center justify-center p-4 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-[#101a22]/50">
                        <div class="p-3 bg-slate-100 dark:bg-[#1c2630] rounded-full mb-3">
                            <x-lucide-loader-2 class="size-5 text-slate-400" />
                        </div>
                        <span class="text-sm text-slate-500 font-medium">No tasks</span>
                    </div>
                </div>

                {{-- Done Column --}}
                <div class="flex flex-col w-80 shrink-0">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <div class="flex items-center gap-2">
                            <div class="size-2 rounded-full bg-emerald-500"></div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">Done</h3>
                            <span class="px-2 py-0.5 rounded-full bg-slate-200 dark:bg-[#283239] text-xs font-medium text-slate-600 dark:text-slate-400">0</span>
                        </div>
                    </div>
                    <div class="flex-1 flex flex-col items-center justify-center p-4 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-[#101a22]/50">
                        <div class="p-3 bg-slate-100 dark:bg-[#1c2630] rounded-full mb-3">
                            <x-lucide-check-circle class="size-5 text-slate-400" />
                        </div>
                        <span class="text-sm text-slate-500 font-medium">No tasks</span>
                    </div>
                </div>
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
