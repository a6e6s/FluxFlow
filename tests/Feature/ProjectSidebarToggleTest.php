<?php

declare(strict_types=1);

use App\Livewire\ProjectSidebar;
use App\Models\Project;
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
