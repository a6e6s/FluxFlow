<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectProgressResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $total = (int) $this->tasks_count;
        $done = (int) $this->done_tasks_count;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'icon' => $this->icon,
            'color' => $this->color,
            'tasks_count' => $total,
            'done_tasks_count' => $done,
            'progress_percent' => $total > 0 ? (int) round($done / $total * 100) : 0,
        ];
    }
}
