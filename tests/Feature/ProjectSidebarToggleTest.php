<?php

declare(strict_types=1);

use App\Livewire\ProjectSidebar;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('project sidebar collapses to icon only and persists across refreshes', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    Project::factory()->for($user)->create([
        'title' => 'Roadmap',
        'color' => '#1392ec',
    ]);

    actingAs($user);

    Livewire::test(ProjectSidebar::class)
        ->assertSet('collapsed', false)
        ->assertSee('w-80', false)
        ->assertSee('aria-label="Collapse sidebar"', false)
        ->assertSee(__('app.active_projects'))
        ->assertSee('aria-label="'.__('app.new_project').'"', false)
        ->call('toggleSidebar')
        ->assertSet('collapsed', true)
        ->assertSee('w-20', false)
        ->assertSee('aria-label="Expand sidebar"', false)
        ->assertDontSee(__('app.active_projects'));

    Livewire::test(ProjectSidebar::class)
        ->assertSet('collapsed', true)
        ->assertSee('w-20', false)
        ->assertSee('aria-label="Expand sidebar"', false);
});

test('project sidebar groups shared projects separately from owned projects', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    /** @var User $owner */
    $owner = User::factory()->create();

    Project::factory()->for($user)->create([
        'title' => 'Owned Project',
        'color' => '#1392ec',
    ]);

    $sharedProject = Project::factory()->for($owner)->create([
        'title' => 'Shared Project',
        'color' => '#10b981',
    ]);

    $sharedTask = Task::factory()->for($sharedProject)->create();
    $sharedTask->collaborators()->attach($user->id);

    actingAs($user);

    Livewire::test(ProjectSidebar::class)
        ->assertSee(__('app.active_projects'))
        ->assertSee(__('app.shared_projects'))
        ->assertSee('Owned Project')
        ->assertSee('Shared Project')
        ->assertDontSee(__('app.no_projects'));
});
