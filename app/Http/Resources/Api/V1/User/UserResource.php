<?php

namespace App\Http\Resources\Api\V1\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->roles->map(fn($role) => [
                'name' => $role->name,
                'label' => ucfirst($role->name), // Делаем первую букву заглавной для красоты
            ]),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
