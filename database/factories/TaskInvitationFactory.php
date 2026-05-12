<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TaskInvitation>
 */
class TaskInvitationFactory extends Factory
{
    protected $model = TaskInvitation::class;

    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'invited_by_id' => User::factory(),
            'email' => $this->faker->unique()->safeEmail(),
            'token' => Str::random(64),
            'accepted_at' => null,
            'declined_at' => null,
            'expires_at' => null,
        ];
    }

    public function accepted(): self
    {
        return $this->state(fn () => ['accepted_at' => now()]);
    }

    public function declined(): self
    {
        return $this->state(fn () => ['declined_at' => now()]);
    }

    public function expired(): self
    {
        return $this->state(fn () => ['expires_at' => now()->subDay()]);
    }
}
