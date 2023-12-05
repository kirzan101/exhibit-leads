<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'department' => $this->department,
            'description' => $this->description,
            'property_id' => $this->property_id,
            'code' => $this->code,
            'property' => $this->property,
            'user_group_permissions' => UserGroupPermissionResource::collection($this->user_group_permissions),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
