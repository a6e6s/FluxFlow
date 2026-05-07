<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\Priority;
use App\Models\Project;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'filter' => ['sometimes', 'array'],
            'filter.priority' => ['sometimes', Rule::in(array_map(static fn (Priority $priority): string => $priority->value, Priority::cases()))],
            'filter.status' => ['sometimes', Rule::in(Project::filterableStatuses())],
            'sort' => [
                'sometimes',
                'string',
                function (string $attribute, mixed $value, Closure $fail): void {
                    foreach ($this->sortsFrom($value) as $sort) {
                        if (! in_array(ltrim($sort, '-'), Project::sortableFields(), true)) {
                            $fail('The selected sort is invalid.');

                            return;
                        }
                    }
                },
            ],
        ];
    }

    public function filters(): array
    {
        return $this->validated('filter', []);
    }

    public function sorts(): array
    {
        return $this->sortsFrom($this->validated('sort'));
    }

    private function sortsFrom(mixed $value): array
    {
        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode(',', $value)),
            static fn (string $sort): bool => $sort !== ''
        ));
    }
}
