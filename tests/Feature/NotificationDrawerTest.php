<?php

declare(strict_types=1);

use App\Livewire\NotificationDrawer;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskInvitation;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->invited = User::factory()->create(['email' => 'invited@example.com']);
    $this->project = Project::factory()->for($this->owner)->create();
    $this->task = Task::factory()->for($this->project)->create();
});

it('lists pending invitations for the current user with an unread badge count', function () {
    TaskInvitation::factory()->create([
        'task_id' => $this->task->id,
        'invited_by_id' => $this->owner->id,
        'email' => $this->invited->email,
    ]);

    $this->actingAs($this->invited);

    Livewire::test(NotificationDrawer::class)
        ->assertSee($this->task->title)
        ->assertSet('open', false)
        ->assertSeeHtml('1')
        ->call('toggle')
        ->assertSet('open', true);
});

it('hides accepted, declined, and expired invitations', function () {
    $task2 = Task::factory()->for($this->project)->create();
    $task3 = Task::factory()->for($this->project)->create();

    TaskInvitation::factory()->create([
        'task_id' => $this->task->id,
        'invited_by_id' => $this->owner->id,
        'email' => $this->invited->email,
        'accepted_at' => now(),
    ]);
    TaskInvitation::factory()->create([
        'task_id' => $task2->id,
        'invited_by_id' => $this->owner->id,
        'email' => $this->invited->email,
        'declined_at' => now(),
    ]);
    TaskInvitation::factory()->create([
        'task_id' => $task3->id,
        'invited_by_id' => $this->owner->id,
        'email' => $this->invited->email,
        'expires_at' => now()->subDay(),
    ]);

    $this->actingAs($this->invited);

    Livewire::test(NotificationDrawer::class)
        ->assertSet('open', false)
        ->assertSeeText(__('invitations.drawer.empty_title'));
});

it('accepts an invitation and adds the user as collaborator', function () {
    $invitation = TaskInvitation::factory()->create([
        'task_id' => $this->task->id,
        'invited_by_id' => $this->owner->id,
        'email' => $this->invited->email,
    ]);

    $this->actingAs($this->invited);

    Livewire::test(NotificationDrawer::class)
        ->call('accept', $invitation->id);

    expect($invitation->fresh()->accepted_at)->not->toBeNull();
    expect($this->task->collaborators()->where('users.id', $this->invited->id)->exists())->toBeTrue();
});

it('declines an invitation without attaching the user', function () {
    $invitation = TaskInvitation::factory()->create([
        'task_id' => $this->task->id,
        'invited_by_id' => $this->owner->id,
        'email' => $this->invited->email,
    ]);

    $this->actingAs($this->invited);

    Livewire::test(NotificationDrawer::class)
        ->call('decline', $invitation->id);

    expect($invitation->fresh()->declined_at)->not->toBeNull();
    expect($invitation->fresh()->status())->toBe('declined');
    expect($this->task->collaborators()->where('users.id', $this->invited->id)->exists())->toBeFalse();
});

it('does not let another user accept or decline someone else\'s invitation', function () {
    $invitation = TaskInvitation::factory()->create([
        'task_id' => $this->task->id,
        'invited_by_id' => $this->owner->id,
        'email' => $this->invited->email,
    ]);

    $other = User::factory()->create();
    $this->actingAs($other);

    Livewire::test(NotificationDrawer::class)
        ->call('accept', $invitation->id)
        ->call('decline', $invitation->id);

    expect($invitation->fresh()->accepted_at)->toBeNull();
    expect($invitation->fresh()->declined_at)->toBeNull();
});
