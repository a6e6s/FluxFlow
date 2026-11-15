<?php

declare(strict_types=1);

namespace App\Actions\Home;

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Queries behind the home page widgets. Shared by the Livewire widgets and the API.
 */
class HomeInsights
{
    /**
     * Counts of visible tasks due within the period, plus all active projects.
     *
     * @return array{open: int, doing: int, review: int, done: int, active_projects: int}
     */
    public function summary(User $user, string $from, string $to): array
    {
        $countByStatus = Task::query()
            ->visibleTo($user)
            ->whereBetween('due_date', [$from, $to])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'open' => (int) $countByStatus->except(TaskStatus::Done->value)->sum(),
            'doing' => $this->countFor($countByStatus, TaskStatus::Doing),
            'review' => $this->countFor($countByStatus, TaskStatus::Review),
            'done' => $this->countFor($countByStatus, TaskStatus::Done),
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
            ->with('project:id,title')
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
            ->with('project:id,title')
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
            ->with('project:id,title')
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
        return Task::query()
            ->visibleTo($user)
            ->join('projects', 'projects.id', '=', 'tasks.project_id')
            ->whereNull('projects.deleted_at')
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
            ->with('project:id,title')
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
     * @param  SupportCollection<string|int, mixed>  $countByStatus
     */
    private function countFor(SupportCollection $countByStatus, TaskStatus $status): int
    {
        return (int) ($countByStatus[$status->value] ?? 0);
    }
}
