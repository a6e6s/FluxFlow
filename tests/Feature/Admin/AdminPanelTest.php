<?php

use App\Enums\Priority;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Tasks\Pages\CreateTask;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\seed;

test('guests are redirected to the admin login', function () {
    get('/admin')->assertRedirect('/admin/login');
});

test('users without the admin flag cannot access the admin panel', function () {
    actingAs(User::factory()->create(['is_admin' => false]));

    get('/admin')->assertForbidden();
});

test('admins can list projects in the admin panel', function () {
    actingAs(User::factory()->admin()->create());

    Project::factory()->count(2)->create();

    get('/admin/projects')->assertOk();
});

test('switching to Arabic renders the admin panel right-to-left', function () {
    actingAs(User::factory()->admin()->create());

    get(route('language.switch', 'ar'));

    get('/admin')->assertOk()->assertSee('dir="rtl"', escape: false);
});

test('task descriptions are sanitized when saved from the admin panel', function () {
    actingAs(User::factory()->admin()->create());

    $project = Project::factory()->create();

    Livewire::test(CreateTask::class)
        ->fillForm([
            'project_id' => $project->id,
            'title' => 'Sanitized task',
            'description' => '<p>Safe</p><script>alert(1)</script>',
            'status' => 'todo',
            'priority' => Priority::Low->value,
            'sort_order' => 0,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $description = Task::where('title', 'Sanitized task')->value('description');

    expect($description)->toContain('Safe')->not->toContain('<script');
});

test('the seeded administrator can access the admin panel', function () {
    seed(DatabaseSeeder::class);

    $admin = User::where('email', 'admin@admin.com')->firstOrFail();

    expect($admin->is_admin)->toBeTrue();

    actingAs($admin);

    get('/admin')->assertOk()->assertSee('logo.png', escape: false);
});

test('archiving a project from the admin panel sets archived_at', function () {
    actingAs(User::factory()->admin()->create());

    $project = Project::factory()->create(['archived_at' => null]);

    Livewire::test(ListProjects::class)
        ->callAction(TestAction::make('archive')->table($project));

    expect($project->fresh()->isArchived())->toBeTrue();
});
