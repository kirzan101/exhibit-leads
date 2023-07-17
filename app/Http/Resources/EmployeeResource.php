<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'position' => $this->position,
            'property' => $this->property,
            'user_group' => $this->userGroup,
            'user' => $this->user,
            'full_name' => $this->getFullName(),
            'id' => (int) $this->getKey()
        ];
    }
}
