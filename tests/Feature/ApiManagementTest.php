<?php

declare(strict_types=1);

use App\Models\Project;
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

test('a user cannot access another users project', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    $apiKey = $user->generateApiKey();

    $otherProject = Project::factory()->for(User::factory()->create())->create();

    withToken($apiKey);

    getJson(route('api.v1.projects.show', ['project' => $otherProject], false))
        ->assertNotFound();
});

test('scramble docs expose the api key security scheme', function (): void {
    withoutMiddleware();
    getJson(route('scramble.docs.document'))
        ->assertOk()
        ->assertJsonPath('components.securitySchemes.apiKey.type', 'apiKey')
        ->assertJsonPath('components.securitySchemes.apiKey.in', 'header')
        ->assertJsonPath('components.securitySchemes.apiKey.name', 'X-API-Key');
});
