<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskInvitationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'task_id' => $this->task_id,
            'task_title' => $this->task?->title,
            'inviter_name' => $this->inviter?->name,
            'expires_at' => $this->expires_at?->toDateTimeString(),
            'url' => route('invitations.show', $this->token),
        ];
    }
}
