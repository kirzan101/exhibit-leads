<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpcLeadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'companion_first_name' => $this->companion_first_name,
            'companion_middle_name' => $this->companion_middle_name,
            'companion_last_name' => $this->companion_last_name,
            'address' => $this->address,
            'hotel' => $this->hotel,
            'mobile_number' => $this->mobile_number,
            'occupation' => $this->occupation,
            'age' => $this->age,
            'source' => $this->getSource(),
            'civil_status' => $this->civil_status,
            'date_filled' => $this->date_filled,
            'is_uploaded' => $this->is_uploaded,
            'full_name' => $this->getFullName(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
