<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        if ($task->project?->user_id === $user->id) {
            return true;
        }

        return $task->collaborators()->where('users.id', $user->id)->exists();
    }

    public function update(User $user, Task $task): bool
    {
        return $this->view($user, $task);
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->project?->user_id === $user->id;
    }

    public function invite(User $user, Task $task): bool
    {
        return $task->project?->user_id === $user->id;
    }
}
