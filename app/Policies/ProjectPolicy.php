<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        if ($project->user_id === $user->id) {
            return true;
        }

        return $project->tasks()
            ->whereHas('collaborators', fn ($q) => $q->where('users.id', $user->id))
            ->exists();
    }

    public function update(User $user, Project $project): bool
    {
        return $project->user_id === $user->id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->user_id === $user->id;
    }

    public function archive(User $user, Project $project): bool
    {
        return $project->user_id === $user->id;
    }
}
