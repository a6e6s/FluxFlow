<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Priority;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    public static function filterableStatuses(): array
    {
        return ['active', 'archived', 'all'];
    }

    public static function sortableFields(): array
    {
        return ['sort_order', 'created_at', 'updated_at', 'title', 'priority', 'status'];
    }

    protected $fillable = [
        'user_id',
        'title',
        'icon',
        'color',
        'sort_order',
        'priority',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'priority' => Priority::class,
            'sort_order' => 'integer',
            'archived_at' => 'datetime',
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    // ─────────────────────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        if (($priority = $filters['priority'] ?? null) !== null) {
            $query->where('priority', $priority);
        }

        return match ($filters['status'] ?? null) {
            'active' => $query->active(),
            'archived' => $query->archived(),
            default => $query,
        };
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
                'status' => $query->orderByRaw("case when archived_at is null then 0 else 1 end {$direction}"),
                default => $query->orderBy($field, $direction),
            };
        }

        return $query;
    }

    // ─────────────────────────────────────────────────────────────
    // Accessors & Helpers
    // ─────────────────────────────────────────────────────────────

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }
}
