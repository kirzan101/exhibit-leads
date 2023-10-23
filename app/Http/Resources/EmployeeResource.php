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
            'user_group_id' => $this->user_group_id,
            'property_id' => $this->property_id,
            'user_group' => $this->userGroup,
            'employee_venues' => $this->employeeVenue,
            'user' => $this->user,
            'full_name' => $this->getFullName(),
            'id' => (int) $this->getKey(),
            'venue_id' => $this->venue_id,
            'exhibitor_id' => $this->exhibitor_id,
            'is_active' => $this->is_active,
            'is_changed_passowrd' => $this->is_changed_password
        ];
    }
}
