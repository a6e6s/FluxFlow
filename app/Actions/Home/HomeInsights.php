<?php

declare(strict_types=1);

namespace App\Actions\Home;

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskInvitation;
use App\Models\User;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Queries behind the home page widgets. Shared by the Livewire widgets and the API.
 */
class HomeInsights
{
    /**
     * @return array{open: int, doing: int, review: int, done: int, overdue: int, active_projects: int}
     */
    public function summary(User $user, string $from, string $to): array
    {
        $countByStatus = $this->analyticsTasks($user)
            ->whereBetween('due_date', [$from, $to])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'open' => (int) $countByStatus->except(TaskStatus::Done->value)->sum(),
            'doing' => $this->countFor($countByStatus, TaskStatus::Doing),
            'review' => $this->countFor($countByStatus, TaskStatus::Review),
            'done' => $this->countFor($countByStatus, TaskStatus::Done),
            'overdue' => $this->analyticsTasks($user)->overdue()->count(),
            'active_projects' => Project::query()->visibleTo($user)->active()->count(),
        ];
    }

    /**
     * @return Collection<int, Task>
     */
    public function dueSoon(User $user): Collection
    {
        return Task::query()
            ->visibleTo($user)
            ->with('project:id,title,color')
            ->where(fn (Builder $query) => $query->overdue()
                ->orWhere(fn (Builder $query) => $query->dueToday()->whereNot('status', TaskStatus::Done)))
            ->orderBy('due_date')
            ->limit(8)
            ->get();
    }

    /**
     * Open tasks due within the next seven days.
     *
     * @return Collection<int, Task>
     */
    public function upcomingDeadlines(User $user): Collection
    {
        return Task::query()
            ->visibleTo($user)
            ->with('project:id,title,color')
            ->whereNot('status', TaskStatus::Done)
            ->whereBetween('due_date', [today()->addDay(), today()->addDays(7)])
            ->orderBy('due_date')
            ->limit(8)
            ->get();
    }

    /**
     * Open tasks assigned to the user.
     *
     * @return Collection<int, Task>
     */
    public function myTasks(User $user): Collection
    {
        return Task::query()
            ->visibleTo($user)
            ->with('project:id,title,color')
            ->where('assigned_to', $user->id)
            ->whereNot('status', TaskStatus::Done)
            ->orderBy('due_date')
            ->limit(8)
            ->get();
    }

    /**
     * Active projects with total and completed task counts.
     *
     * @return Collection<int, Project>
     */
    public function projectProgress(User $user): Collection
    {
        return Project::query()
            ->visibleTo($user)
            ->active()
            ->ordered()
            ->withCount([
                'tasks',
                'tasks as done_tasks_count' => fn (Builder $query) => $query->where('status', TaskStatus::Done),
                'tasks as overdue_tasks_count' => fn (Builder $query) => $query->overdue(),
            ])
            ->limit(6)
            ->get();
    }

    /**
     * Open effort points per project for tasks due within the period.
     *
     * @return SupportCollection<int, object{id: int, title: string, effort: int, open_tasks: int}>
     */
    public function effortLoad(User $user, string $from, string $to): SupportCollection
    {
        return $this->analyticsTasks($user)
            ->join('projects', 'projects.id', '=', 'tasks.project_id')
            ->whereNull('projects.deleted_at')
            ->whereNull('projects.archived_at')
            ->whereNot('tasks.status', TaskStatus::Done)
            ->whereBetween('tasks.due_date', [$from, $to])
            ->groupBy('projects.id', 'projects.title')
            ->selectRaw('projects.id, projects.title, coalesce(sum(tasks.effort_score), 0) as effort, count(tasks.id) as open_tasks')
            ->orderByDesc('effort')
            ->limit(6)
            ->get()
            ->toBase();
    }

    /**
     * Most recently updated tasks the user can see.
     *
     * @return Collection<int, Task>
     */
    public function recentActivity(User $user): Collection
    {
        return Task::query()
            ->visibleTo($user)
            ->with('project:id,title,color')
            ->latest('updated_at')
            ->limit(8)
            ->get();
    }

    /**
     * Task invitations addressed to the user's email that are still pending.
     *
     * @return Collection<int, TaskInvitation>
     */
    public function pendingInvitations(User $user): Collection
    {
        return TaskInvitation::query()
            ->pendingFor($user->email)
            ->with(['task:id,title', 'inviter:id,name'])
            ->latest()
            ->limit(5)
            ->get();
    }

    /**
     * Per-day counts within the period, used for the stat card sparklines.
     *
     * @return array{open: list<int>, doing: list<int>, review: list<int>, done: list<int>, active_projects: list<int>}
     */
    public function dailySeries(User $user, string $from, string $to): array
    {
        $days = collect(CarbonPeriod::create($from, $to))
            ->map(fn (CarbonInterface $day): string => $day->toDateString())
            ->values();

        $tasksByDayAndStatus = $this->analyticsTasks($user)
            ->whereBetween('due_date', [$from, $to])
            ->selectRaw('due_date, status, count(*) as total')
            ->groupBy('due_date', 'status')
            ->get()
            ->groupBy(fn ($row): string => substr((string) $row->due_date, 0, 10));

        $countFor = fn (string $day, ?TaskStatus $status): int => (int) ($tasksByDayAndStatus->get($day)?->filter(
            fn ($row): bool => $status === null
                ? $row->status !== TaskStatus::Done
                : $row->status === $status
        )->sum('total') ?? 0);

        $projectsByDay = Project::query()
            ->visibleTo($user)
            ->active()
            ->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->get(['created_at'])
            ->groupBy(fn (Project $project): string => $project->created_at->toDateString());

        return [
            'open' => $days->map(fn (string $day): int => $countFor($day, null))->all(),
            'doing' => $days->map(fn (string $day): int => $countFor($day, TaskStatus::Doing))->all(),
            'review' => $days->map(fn (string $day): int => $countFor($day, TaskStatus::Review))->all(),
            'done' => $days->map(fn (string $day): int => $countFor($day, TaskStatus::Done))->all(),
            'active_projects' => $days->map(fn (string $day): int => $projectsByDay->get($day)?->count() ?? 0)->all(),
        ];
    }

    /**
     * Tasks visible to the user that belong to active (non-archived) projects.
     *
     * @return Builder<Task>
     */
    private function analyticsTasks(User $user): Builder
    {
        return Task::query()
            ->visibleTo($user)
            ->whereHas('project', fn (Builder $query) => $query->active());
    }

    /**
     * @param  SupportCollection<string|int, mixed>  $countByStatus
     */
    private function countFor(SupportCollection $countByStatus, TaskStatus $status): int
    {
        return (int) ($countByStatus[$status->value] ?? 0);
    }
}
