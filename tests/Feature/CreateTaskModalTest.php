<?php

declare(strict_types=1);

use App\Livewire\CreateTaskModal;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('creates a task with the rich text description and compact metadata fields', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->for($owner)->create();

    $this->actingAs($owner);

    Livewire::test(CreateTaskModal::class)
        ->call('openModal', $project->id)
        ->set('title', 'Formatted task')
        ->set('description', '<p>Important <strong>details</strong></p>')
        ->set('priority', 'high')
        ->set('status', 'doing')
        ->set('dueDate', '2026-11-15')
        ->set('effortScore', 5)
        ->call('create')
        ->assertSet('open', false);

    $task = Task::query()->where('project_id', $project->id)->firstOrFail();

    expect($task->title)->toBe('Formatted task')
        ->and($task->description)->toBe('<p>Important <strong>details</strong></p>')
        ->and($task->priority->value)->toBe('high')
        ->and($task->status->value)->toBe('doing')
        ->and($task->due_date?->format('Y-m-d'))->toBe('2026-11-15')
        ->and($task->effort_score)->toBe(5);
});

it('sanitizes unsafe rich text when creating a task', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->for($owner)->create();

    $this->actingAs($owner);

    Livewire::test(CreateTaskModal::class)
        ->call('openModal', $project->id)
        ->set('title', 'Safe task')
        ->set('description', '<p>Keep <strong>formatting</strong></p><img src="x" onerror="alert(1)"><a href="javascript:alert(1)">unsafe link</a>')
        ->call('create');

    $description = Task::query()->where('project_id', $project->id)->firstOrFail()->description;

    expect($description)
        ->toContain('<p>Keep <strong>formatting</strong></p>')
        ->toContain('<a>unsafe link</a>')
        ->not->toContain('<img')
        ->not->toContain('onerror')
        ->not->toContain('javascript:');
});

it('stores uploaded attachments when creating a task', function () {
    Storage::fake('public');

    $owner = User::factory()->create();
    $project = Project::factory()->for($owner)->create();

    $this->actingAs($owner);

    Livewire::test(CreateTaskModal::class)
        ->call('openModal', $project->id)
        ->set('title', 'Task with attachment')
        ->set('files', [UploadedFile::fake()->create('brief.pdf', 32, 'application/pdf')])
        ->call('create')
        ->assertSet('open', false)
        ->assertSet('files', []);

    $task = Task::query()->where('project_id', $project->id)->firstOrFail();
    $attachment = $task->attachments()->firstOrFail();

    expect($attachment->file_name)->toBe('brief.pdf')
        ->and($attachment->file_size)->toBeGreaterThan(0)
        ->and($attachment->file_path)->toStartWith('attachments/'.$project->id.'/');

    Storage::disk('public')->assertExists($attachment->file_path);
});

it('rejects task attachments larger than 10 MB', function () {
    Storage::fake('public');

    $owner = User::factory()->create();
    $project = Project::factory()->for($owner)->create();

    $this->actingAs($owner);

    Livewire::test(CreateTaskModal::class)
        ->call('openModal', $project->id)
        ->set('title', 'Task with oversized attachment')
        ->set('files', [UploadedFile::fake()->create('large.pdf', 10241, 'application/pdf')])
        ->call('create')
        ->assertHasErrors(['files.0' => 'max']);

    expect(Task::query()->where('project_id', $project->id)->exists())->toBeFalse();
});

it('renders the shared Quill editor and compact task metadata fields', function () {
    $owner = User::factory()->create();
    $project = Project::factory()->for($owner)->create();

    $this->actingAs($owner);

    Livewire::test(CreateTaskModal::class)
        ->call('openModal', $project->id)
        ->assertSee('task-description-editor', false)
        ->assertSee('mountDescriptionEditor($el)', false)
        ->assertSee('grid-cols-3 items-end gap-2', false)
        ->assertSee(__('app.status'))
        ->assertSee(__('app.description_placeholder'))
        ->assertSee(__('app.enter_task_title'));
});
