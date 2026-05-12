<?php

declare(strict_types=1);

use App\Actions\Tasks\InviteUserToTask;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

it('stores a database notification when inviting an existing user', function () {
    $owner = User::factory()->create();
    $invitee = User::factory()->create(['email' => 'jane@example.com']);
    $project = Project::factory()->for($owner)->create();
    $task = Task::factory()->for($project)->create();

    $result = app(InviteUserToTask::class)->handle($task, $invitee->email, $owner);

    expect($result['status'])->toBe('invited');
    expect($invitee->notifications()->count())->toBe(1);

    $notification = $invitee->notifications()->first();

    expect($notification)->not->toBeNull();
    expect($notification->data['type'])->toBe('task_invitation');
    expect($notification->data['task_id'])->toBe($task->id);
});
