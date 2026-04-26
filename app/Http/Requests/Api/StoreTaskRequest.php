<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $this->user() !== null
            && $project instanceof Project
            && $project->user_id === $this->user()->getAuthIdentifier();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'priority' => ['required', Rule::in(array_map(static fn (Priority $priority): string => $priority->value, Priority::cases()))],
            'status' => ['required', Rule::in(array_map(static fn (TaskStatus $status): string => $status->value, TaskStatus::cases()))],
            'sort_order' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'effort_score' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:10'],
            'assigned_to' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
        ];
    }
}
