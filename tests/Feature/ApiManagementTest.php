<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\withoutMiddleware;
use function Pest\Laravel\withToken;

test('api requests require an api key', function (): void {
    getJson(route('api.v1.projects.index', [], false))
        ->assertUnauthorized();
});

test('a user can manage projects and tasks through the api', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    $apiKey = $user->generateApiKey();

    withToken($apiKey);

    $projectResponse = postJson(route('api.v1.projects.store', [], false), [
        'title' => 'Website redesign',
        'icon' => '🚀',
        'color' => '#3b82f6',
        'priority' => 'high',
    ])
        ->assertCreated()
        ->assertJsonPath('data.title', 'Website redesign');

    $projectId = $projectResponse->json('data.id');

    getJson(route('api.v1.projects.index', [], false))
        ->assertOk()
        ->assertJsonCount(1, 'data');

    $taskResponse = postJson(route('api.v1.projects.tasks.store', ['project' => $projectId], false), [
        'title' => 'Draft homepage copy',
        'description' => 'Prepare the first version of the landing page copy.',
        'priority' => 'medium',
        'status' => 'todo',
        'effort_score' => 5,
    ])
        ->assertCreated()
        ->assertJsonPath('data.title', 'Draft homepage copy');

    $taskId = $taskResponse->json('data.id');

    getJson(route('api.v1.projects.tasks.index', ['project' => $projectId], false))
        ->assertOk()
        ->assertJsonCount(1, 'data');

    patchJson(route('api.v1.projects.tasks.update', ['project' => $projectId, 'task' => $taskId], false), [
        'status' => 'doing',
    ])
        ->assertOk()
        ->assertJsonPath('data.status', 'doing');

    deleteJson(route('api.v1.projects.tasks.destroy', ['project' => $projectId, 'task' => $taskId], false))
        ->assertNoContent();
});

test('projects api supports filtering and sorting', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    $apiKey = $user->generateApiKey();

    Project::factory()->for($user)->archived()->create([
        'title' => 'Alpha archived project',
        'priority' => 'high',
        'sort_order' => 1,
    ]);

    Project::factory()->for($user)->create([
        'title' => 'Beta project',
        'priority' => 'high',
        'sort_order' => 2,
    ]);

    Project::factory()->for($user)->create([
        'title' => 'Gamma project',
        'priority' => 'medium',
        'sort_order' => 3,
    ]);

    Project::factory()->for($user)->create([
        'title' => 'Delta project',
        'priority' => 'low',
        'sort_order' => 4,
    ]);

    withToken($apiKey);

    $sortedResponse = getJson(route('api.v1.projects.index', [], false).'?sort=-priority,title')
        ->assertOk();

    expect(collect($sortedResponse->json('data'))->pluck('title')->all())
        ->toBe([
            'Alpha archived project',
            'Beta project',
            'Gamma project',
            'Delta project',
        ]);

    getJson(route('api.v1.projects.index', [], false).'?'.http_build_query([
        'filter' => [
            'priority' => 'high',
            'status' => 'archived',
        ],
    ]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Alpha archived project');
});

test('tasks api supports filtering and sorting', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    $apiKey = $user->generateApiKey();
    $project = Project::factory()->for($user)->create();

    Task::factory()->for($project)->create([
        'title' => 'Alpha task',
        'priority' => 'high',
        'status' => 'todo',
        'sort_order' => 3,
        'due_date' => today()->addDays(2)->toDateString(),
    ]);

    Task::factory()->for($project)->create([
        'title' => 'Review task',
        'priority' => 'high',
        'status' => 'review',
        'sort_order' => 1,
        'due_date' => today()->toDateString(),
    ]);

    Task::factory()->for($project)->create([
        'title' => 'Overdue task',
        'priority' => 'medium',
        'status' => 'doing',
        'sort_order' => 2,
        'due_date' => today()->subDay()->toDateString(),
    ]);

    Task::factory()->for($project)->create([
        'title' => 'No due task',
        'priority' => 'low',
        'status' => 'done',
        'sort_order' => 4,
        'due_date' => null,
    ]);

    withToken($apiKey);

    $statusSortedResponse = getJson(route('api.v1.projects.tasks.index', ['project' => $project], false).'?sort=-priority,status')
        ->assertOk();

    expect(collect($statusSortedResponse->json('data'))->pluck('title')->all())
        ->toBe([
            'Alpha task',
            'Review task',
            'Overdue task',
            'No due task',
        ]);

    $dueDateSortedResponse = getJson(route('api.v1.projects.tasks.index', ['project' => $project], false).'?sort=due_date')
        ->assertOk();

    expect(collect($dueDateSortedResponse->json('data'))->pluck('title')->all())
        ->toBe([
            'Overdue task',
            'Review task',
            'Alpha task',
            'No due task',
        ]);

    getJson(route('api.v1.projects.tasks.index', ['project' => $project], false).'?'.http_build_query([
        'filter' => [
            'status' => 'review',
            'priority' => 'high',
            'due_date' => today()->toDateString(),
        ],
    ]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Review task');

    getJson(route('api.v1.projects.tasks.index', ['project' => $project], false).'?'.http_build_query([
        'filter' => [
            'due_state' => 'overdue',
        ],
    ]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Overdue task');

    getJson(route('api.v1.projects.tasks.index', ['project' => $project], false).'?'.http_build_query([
        'filter' => [
            'status' => 'all',
        ],
    ]))
        ->assertOk()
        ->assertJsonCount(4, 'data');
});

test('a user cannot access another users project', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    $apiKey = $user->generateApiKey();

    $otherProject = Project::factory()->for(User::factory()->create())->create();

    withToken($apiKey);

    getJson(route('api.v1.projects.show', ['project' => $otherProject], false))
        ->assertNotFound()
        ->assertJsonPath('message', 'Project not found.');
});

test('a missing project returns a sanitized not found response', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    $apiKey = $user->generateApiKey();

    withToken($apiKey);

    getJson(route('api.v1.projects.show', ['project' => 999999], false))
        ->assertNotFound()
        ->assertJsonPath('message', 'Project not found.')
        ->assertJsonMissingPath('exception')
        ->assertJsonMissingPath('file')
        ->assertJsonMissingPath('line')
        ->assertJsonMissingPath('trace');
});

test('scramble docs expose the api key security scheme', function (): void {
    withoutMiddleware();
    getJson(route('scramble.docs.document'))
        ->assertOk()
        ->assertJsonPath('components.securitySchemes.apiKey.type', 'apiKey')
        ->assertJsonPath('components.securitySchemes.apiKey.in', 'header')
        ->assertJsonPath('components.securitySchemes.apiKey.name', 'X-API-Key');
});
