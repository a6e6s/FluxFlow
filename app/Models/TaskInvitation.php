<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TaskInvitationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TaskInvitation extends Model
{
    /** @use HasFactory<TaskInvitationFactory> */
    use HasFactory;

    protected $fillable = [
        'task_id',
        'invited_by_id',
        'email',
        'token',
        'accepted_at',
        'declined_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'declined_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $invitation): void {
            if (! $invitation->token) {
                $invitation->token = self::generateToken();
            }
        });
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_id');
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function isDeclined(): bool
    {
        return $this->declined_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isPending(): bool
    {
        return ! $this->isAccepted() && ! $this->isDeclined() && ! $this->isExpired();
    }

    public function status(): string
    {
        return match (true) {
            $this->isAccepted() => 'accepted',
            $this->isDeclined() => 'declined',
            $this->isExpired() => 'expired',
            default => 'pending',
        };
    }

    /**
     * @param  Builder<TaskInvitation>  $query
     * @return Builder<TaskInvitation>
     */
    public function scopePendingFor($query, string $email)
    {
        return $query
            ->where('email', mb_strtolower($email))
            ->whereNull('accepted_at')
            ->whereNull('declined_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }
}
