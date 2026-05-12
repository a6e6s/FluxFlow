<?php

declare(strict_types=1);

use App\Actions\Tasks\InviteUserToTask;
use App\Actions\Tasks\ResolvePendingInvitations;
use App\Mail\TaskInvitationMail;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskInvitation;
use App\Models\User;
use App\Notifications\TaskInvitationNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Mail::fake();
    Notification::fake();

    $this->owner = User::factory()->create();
    $this->project = Project::factory()->for($this->owner)->create();
    $this->task = Task::factory()->for($this->project)->create();
});

it('creates a pending invitation for an existing user and notifies them via DB', function () {
    $existing = User::factory()->create(['email' => 'jane@example.com']);

    $result = app(InviteUserToTask::class)->handle($this->task, 'jane@example.com', $this->owner);

    expect($result['status'])->toBe('invited');
    expect($result['invitation'])->toBeInstanceOf(TaskInvitation::class);
    expect($result['invitation']->accepted_at)->toBeNull();
    expect($result['invitation']->declined_at)->toBeNull();
    expect($this->task->collaborators()->where('users.id', $existing->id)->exists())->toBeFalse();

    Notification::assertSentTo($existing, TaskInvitationNotification::class);
    Mail::assertNothingSent();
});

it('creates a pending invitation and emails a non-existing user', function () {
    $result = app(InviteUserToTask::class)->handle($this->task, 'newbie@example.com', $this->owner);

    expect($result['status'])->toBe('invited');
    expect($result['invitation'])->toBeInstanceOf(TaskInvitation::class);
    expect($result['invitation']->accepted_at)->toBeNull();

    Mail::assertSent(TaskInvitationMail::class, function (TaskInvitationMail $mail) {
        return $mail->hasTo('newbie@example.com');
    });
});

it('rejects inviting the project owner', function () {
    $result = app(InviteUserToTask::class)->handle($this->task, $this->owner->email, $this->owner);

    expect($result['status'])->toBe('owner');
    expect($this->task->collaborators()->count())->toBe(0);
});

it('detects an already-collaborator email', function () {
    $existing = User::factory()->create();
    $this->task->collaborators()->attach($existing->id);

    $result = app(InviteUserToTask::class)->handle($this->task, $existing->email, $this->owner);

    expect($result['status'])->toBe('already');
});

it('resolves pending invitations when the invited user registers', function () {
    $invitation = TaskInvitation::factory()->create([
        'task_id' => $this->task->id,
        'invited_by_id' => $this->owner->id,
        'email' => 'late@example.com',
    ]);

    $newUser = User::factory()->create(['email' => 'late@example.com']);

    $count = app(ResolvePendingInvitations::class)->handle($newUser);

    expect($count)->toBe(1);
    expect($this->task->collaborators()->where('users.id', $newUser->id)->exists())->toBeTrue();
    expect($invitation->fresh()->accepted_at)->not->toBeNull();
});

it('is idempotent when re-inviting the same email', function () {
    app(InviteUserToTask::class)->handle($this->task, 'twice@example.com', $this->owner);
    app(InviteUserToTask::class)->handle($this->task, 'twice@example.com', $this->owner);

    expect(TaskInvitation::where('task_id', $this->task->id)->where('email', 'twice@example.com')->count())->toBe(1);
});
