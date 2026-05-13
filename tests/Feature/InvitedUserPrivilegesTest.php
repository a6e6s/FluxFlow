<?php

declare(strict_types=1);

use App\Livewire\KanbanBoard;
use App\Livewire\TaskDetails;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->collaborator = User::factory()->create();
    $this->project = Project::factory()->for($this->owner)->create();
});

it('shows only invited tasks to a collaborator on the kanban board', function () {
    $invitedTask = Task::factory()->for($this->project)->create();
    $hiddenTask = Task::factory()->for($this->project)->create();
    $invitedTask->collaborators()->attach($this->collaborator->id);

    $this->actingAs($this->collaborator);

    Livewire::test(KanbanBoard::class, ['projectId' => $this->project->id])
        ->assertSee($invitedTask->title)
        ->assertDontSee($hiddenTask->title);
});

it('lets collaborators update only the task description', function () {
    $task = Task::factory()->for($this->project)->create([
        'title' => 'Original',
        'description' => 'Original description',
    ]);
    $task->collaborators()->attach($this->collaborator->id);

    $this->actingAs($this->collaborator);

    Livewire::test(TaskDetails::class)
        ->call('openTask', $task->id)
        ->set('title', 'Hacked')
        ->set('description', 'Collaborator update')
        ->call('save');

    expect($task->fresh()->title)->toBe('Original');
    expect($task->fresh()->description)->toBe('Collaborator update');
});

it('allows project owner to edit task fields', function () {
    $task = Task::factory()->for($this->project)->create(['title' => 'Original']);

    $this->actingAs($this->owner);

    Livewire::test(TaskDetails::class)
        ->call('openTask', $task->id)
        ->set('title', 'Updated')
        ->call('save');

    expect($task->fresh()->title)->toBe('Updated');
});

it('lets project owners delete their own tasks from task details', function () {
    $task = Task::factory()->for($this->project)->create(['title' => 'Delete me']);

    $this->actingAs($this->owner);

    Livewire::test(TaskDetails::class)
        ->call('openTask', $task->id)
        ->assertSee(__('app.delete_task'))
        ->call('deleteTask');

    expect(Task::withTrashed()->find($task->id)?->trashed())->toBeTrue();
});

it('hides add task actions for collaborators on projects they do not own', function () {
    $task = Task::factory()->for($this->project)->create();
    $task->collaborators()->attach($this->collaborator->id);

    $this->actingAs($this->collaborator);

    Livewire::test(KanbanBoard::class, ['projectId' => $this->project->id])
        ->assertDontSee(__('app.add_task'));
});

it('hides edit and archive actions for collaborators on projects they do not own', function () {
    $task = Task::factory()->for($this->project)->create();
    $task->collaborators()->attach($this->collaborator->id);

    $this->actingAs($this->collaborator);

    Livewire::test(KanbanBoard::class, ['projectId' => $this->project->id])
        ->assertDontSee(__('app.edit'))
        ->assertDontSee(__('app.archive'));
});

it('hides projects from users who are not collaborators on any task', function () {
    $other = User::factory()->create();

    $this->actingAs($other);

    $visible = Project::query()->visibleTo($other)->pluck('id');

    expect($visible)->not->toContain($this->project->id);
});

it('exposes a project to a user who collaborates on at least one of its tasks', function () {
    $task = Task::factory()->for($this->project)->create();
    $task->collaborators()->attach($this->collaborator->id);

    $visible = Project::query()->visibleTo($this->collaborator)->pluck('id');

    expect($visible)->toContain($this->project->id);
});

it('forbids non-owners from updating the project via policy', function () {
    expect($this->collaborator->can('update', $this->project))->toBeFalse();
    expect($this->owner->can('update', $this->project))->toBeTrue();
});
