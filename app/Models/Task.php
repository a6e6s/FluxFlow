<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    public static function filterableStatuses(): array
    {
        return [
            ...array_map(static fn (TaskStatus $status): string => $status->value, TaskStatus::cases()),
            'all',
        ];
    }

    public static function filterableDueStates(): array
    {
        return ['overdue', 'today', 'upcoming', 'none'];
    }

    public static function sortableFields(): array
    {
        return ['sort_order', 'created_at', 'updated_at', 'title', 'priority', 'status', 'due_date', 'effort_score'];
    }

    protected $fillable = [
        'project_id',
        'assigned_to',
        'title',
        'description',
        'priority',
        'status',
        'sort_order',
        'due_date',
        'effort_score',
    ];

    protected function casts(): array
    {
        return [
            'priority' => Priority::class,
            'status' => TaskStatus::class,
            'sort_order' => 'integer',
            'due_date' => 'date',
            'effort_score' => 'integer',
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    // ─────────────────────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────────────────────

    public function scopeTodo($query)
    {
        return $query->where('status', TaskStatus::Todo);
    }

    public function scopeDoing($query)
    {
        return $query->where('status', TaskStatus::Doing);
    }

    public function scopeDone($query)
    {
        return $query->where('status', TaskStatus::Done);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        if (($status = $filters['status'] ?? null) !== null && $status !== 'all') {
            $query->where('status', $status);
        }

        if (($priority = $filters['priority'] ?? null) !== null) {
            $query->where('priority', $priority);
        }

        if (($dueDate = $filters['due_date'] ?? null) !== null) {
            $query->whereDate('due_date', $dueDate);
        }

        match ($filters['due_state'] ?? null) {
            'overdue' => $query->overdue(),
            'today' => $query->dueToday(),
            'upcoming' => $query->whereDate('due_date', '>', today()),
            'none' => $query->whereNull('due_date'),
            default => null,
        };

        return $query;
    }

    public function scopeSort(Builder $query, array $sorts)
    {
        if ($sorts === []) {
            return $query->ordered();
        }

        foreach ($sorts as $sort) {
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $field = ltrim($sort, '-');

            match ($field) {
                'priority' => $query->orderByRaw("case priority when 'low' then 0 when 'medium' then 1 when 'high' then 2 else 3 end {$direction}"),
                'status' => $query->orderByRaw("case status when 'todo' then 0 when 'doing' then 1 when 'review' then 2 when 'done' then 3 else 4 end {$direction}"),
                'due_date' => $query->orderByRaw('due_date is null')->orderBy('due_date', $direction),
                default => $query->orderBy($field, $direction),
            };
        }

        return $query;
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->where('status', '!=', TaskStatus::Done);
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today());
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    // ─────────────────────────────────────────────────────────────
    // Accessors & Helpers
    // ─────────────────────────────────────────────────────────────

    public function isOverdue(): bool
    {
        return $this->due_date !== null
            && $this->due_date->isBefore(today())
            && $this->status !== TaskStatus::Done;
    }

    public function isDueToday(): bool
    {
        return $this->due_date?->isToday() ?? false;
    }

    public function isCompleted(): bool
    {
        return $this->status === TaskStatus::Done;
    }
}
