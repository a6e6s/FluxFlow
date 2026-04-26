<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');
        $task = $this->route('task');

        return $this->user() !== null
            && $project instanceof Project
            && $task instanceof Task
            && $project->user_id === $this->user()->getAuthIdentifier()
            && $task->project_id === $project->getKey();
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'priority' => ['sometimes', 'required', Rule::in(array_map(static fn (Priority $priority): string => $priority->value, Priority::cases()))],
            'status' => ['sometimes', 'required', Rule::in(array_map(static fn (TaskStatus $status): string => $status->value, TaskStatus::cases()))],
            'sort_order' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'effort_score' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:10'],
            'assigned_to' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
        ];
    }
}
