<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Models\Project;
use Illuminate\Routing\Route as RouteBinding;
use Illuminate\Support\Facades\Route;

Route::bind('project', function (string $value) {
    $user = request()->user();

    abort_unless($user !== null, 401);

    return $user->projects()->whereKey($value)->firstOrFail();
});

Route::bind('task', function (string $value, RouteBinding $route) {
    $project = $route->parameter('project');

    abort_unless($project instanceof Project, 404);

    return $project->tasks()->whereKey($value)->firstOrFail();
});

Route::prefix('v1')
    ->as('api.v1.')
    ->group(function (): void {
        Route::apiResource('projects', ProjectController::class);
        Route::apiResource('projects.tasks', TaskController::class);
    });
