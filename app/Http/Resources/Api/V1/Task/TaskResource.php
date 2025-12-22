<?php

namespace App\Http\Resources\Api\V1\Task;

use App\Http\Resources\Api\V1\Project\ProjectResource;
use App\Http\Resources\Api\V1\User\UserResource;
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
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'status_label' => $this->status->label(),
            'priority' => $this->priority,
            'priority_label' => $this->priority->label(),
            'order' => $this->order,
            'time_spent' => $this->time_spent,
            'due_date' => $this->due_date ? $this->due_date->format('Y-m-d') : null,
            'project' => ProjectResource::make($this->whenLoaded('project')),
            'assignee' => UserResource::make($this->whenLoaded('assignee')),
            'creator' => UserResource::make($this->whenLoaded('creator')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
