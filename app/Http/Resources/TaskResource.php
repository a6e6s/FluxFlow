<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'project_name' => $this->whenLoaded('project', fn (): ?string => $this->project?->title),
            'assigned_to' => $this->assigned_to,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority?->value,
            'status' => $this->status?->value,
            'sort_order' => $this->sort_order,
            'due_date' => $this->due_date?->toDateString(),
            'effort_score' => $this->effort_score,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
