<?php

declare(strict_types=1);

use App\Livewire\ProjectSidebar;
use App\Models\Project;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('project sidebar items use stronger light mode contrast', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    Project::factory()->for($user)->create([
        'title' => 'Important Project',
        'color' => '#1392ec',
    ]);

    actingAs($user);

    Livewire::test(ProjectSidebar::class)
        ->assertSee('style="border-color: #1392ec40"', false)
        ->assertSee('hover:bg-slate-100 hover:border-slate-300 dark:hover:bg-slate-800/50', false)
        ->call('selectProject', $user->projects()->first()->id)
        ->assertSee('style="border-color: #1392ec20"', false);
});
