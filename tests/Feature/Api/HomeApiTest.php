<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskInvitation;
use App\Models\User;

use function Pest\Laravel\getJson;
use function Pest\Laravel\withToken;

function signInWithApiKey(): User
{
    $user = User::factory()->create(['email' => 'api-user@example.com']);

    withToken($user->generateApiKey());

    return $user;
}

test('home endpoints require an api key', function (): void {
    getJson('/api/v1/home/summary')->assertUnauthorized();
});

test('every home endpoint responds with data', function (): void {
    signInWithApiKey();

    foreach (['summary', 'due-soon', 'upcoming-deadlines', 'my-tasks', 'project-progress', 'effort-load', 'recent-activity', 'invitations'] as $endpoint) {
        getJson("/api/v1/home/{$endpoint}")->assertOk()->assertJsonStructure(['data']);
    }
});

test('summary counts tasks due in the requested period', function (): void {
    $user = signInWithApiKey();
    $project = Project::factory()->create(['user_id' => $user->id]);

    Task::factory()->create(['project_id' => $project->id, 'status' => 'done', 'due_date' => today()]);
    Task::factory()->create(['project_id' => $project->id, 'status' => 'doing', 'due_date' => today()->subMonthNoOverflow()]);

    getJson('/api/v1/home/summary')
        ->assertOk()
        ->assertJsonPath('data.done', 1)
        ->assertJsonPath('data.doing', 0);

    getJson('/api/v1/home/summary?period=last_month')
        ->assertOk()
        ->assertJsonPath('data.doing', 1)
        ->assertJsonPath('data.done', 0);
});

test('home analytics exclude tasks from archived projects', function (): void {
    $user = signInWithApiKey();
    $activeProject = Project::factory()->create(['user_id' => $user->id]);
    $archivedProject = Project::factory()->archived()->create(['user_id' => $user->id]);

    Task::factory()->create(['project_id' => $activeProject->id, 'status' => 'doing', 'due_date' => today(), 'effort_score' => 2]);
    Task::factory()->create(['project_id' => $archivedProject->id, 'status' => 'doing', 'due_date' => today(), 'effort_score' => 5]);
    Task::factory()->create(['project_id' => $archivedProject->id, 'status' => 'todo', 'due_date' => today()->subDay()]);

    getJson('/api/v1/home/summary')
        ->assertOk()
        ->assertJsonPath('data.doing', 1)
        ->assertJsonPath('data.open', 1)
        ->assertJsonPath('data.overdue', 0);

    getJson('/api/v1/home/due-soon')
        ->assertOk()
        ->assertJsonCount(1, 'data');

    getJson('/api/v1/home/effort-load')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $activeProject->id)
        ->assertJsonPath('data.0.effort', 2);
});

test('custom period requires valid dates', function (): void {
    signInWithApiKey();

    getJson('/api/v1/home/effort-load?period=custom&from=2026-03-10&to=2026-03-01')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['to']);

    getJson('/api/v1/home/effort-load?period=custom')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['from', 'to']);
});

test('project progress and task lists only include the user\'s visible data', function (): void {
    $user = signInWithApiKey();
    $mine = Project::factory()->create(['user_id' => $user->id, 'title' => 'Mine']);
    $foreign = Project::factory()->create(['title' => 'Foreign']);

    Task::factory()->create(['project_id' => $mine->id, 'assigned_to' => $user->id, 'status' => 'todo', 'title' => 'My open task']);
    Task::factory()->create(['project_id' => $foreign->id, 'title' => 'Foreign task']);

    getJson('/api/v1/home/project-progress')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Mine');

    getJson('/api/v1/home/my-tasks')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'My open task');
});

test('pending invitations only list invitations addressed to the user', function (): void {
    $user = signInWithApiKey();
    $inviter = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $inviter->id]);
    $task = Task::factory()->create(['project_id' => $project->id, 'title' => 'Shared task']);

    TaskInvitation::factory()->create(['task_id' => $task->id, 'invited_by_id' => $inviter->id, 'email' => $user->email]);
    TaskInvitation::factory()->create(['task_id' => $task->id, 'invited_by_id' => $inviter->id, 'email' => 'someone-else@example.com']);

    getJson('/api/v1/home/invitations')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.task_title', 'Shared task');
});
