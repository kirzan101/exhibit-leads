<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserGroupPermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->getKey(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_group_id' => $this->user_group_id,
            'user_group' => $this->userGroup,
            'permission_id' => $this->permission_id,
            'permission' => $this->permission
        ];
    }
}
