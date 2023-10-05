<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'event' => $this->event,
            'status' => $this->status,
            'browser' => $this->browser,
            'properties' => $this->properties,
            'subject_id' => $this->subject_id,
            'causer_id' => $this->causer_id,
            'causer' => $this->causer->employee,
            'causer_full_name' => $this->causer->employee->getFullName(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String()
        ];
    }
}
