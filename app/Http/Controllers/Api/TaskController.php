<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IndexTaskRequest;
use App\Http\Requests\Api\StoreTaskRequest;
use App\Http\Requests\Api\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    public function index(IndexTaskRequest $request, Project $project): AnonymousResourceCollection
    {
        return TaskResource::collection(
            $project->tasks()
                ->filter($request->filters())
                ->sort($request->sorts())
                ->get()
        );
    }

    public function store(StoreTaskRequest $request, Project $project): JsonResponse
    {
        $validated = $request->validated();
        $status = $validated['status'];
        $sortOrder = $validated['sort_order'] ?? (($project->tasks()->where('status', $status)->max('sort_order') ?? -1) + 1);

        $task = $project->tasks()->create([
            'assigned_to' => $validated['assigned_to'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'status' => $status,
            'sort_order' => $sortOrder,
            'due_date' => $validated['due_date'] ?? null,
            'effort_score' => $validated['effort_score'] ?? null,
        ]);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Project $project, Task $task): TaskResource
    {
        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task): TaskResource
    {
        $validated = $request->validated();

        if (array_key_exists('status', $validated) && ! array_key_exists('sort_order', $validated) && $validated['status'] !== $task->status->value) {
            $validated['sort_order'] = ($project->tasks()
                ->where('status', $validated['status'])
                ->where('id', '!=', $task->getKey())
                ->max('sort_order') ?? -1) + 1;
        }

        $task->update($validated);

        return new TaskResource($task->refresh());
    }

    public function destroy(Project $project, Task $task): Response
    {
        $task->delete();

        return response()->noContent();
    }
}
