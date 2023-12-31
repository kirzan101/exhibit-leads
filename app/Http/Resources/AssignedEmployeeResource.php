<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignedEmployeeResource extends JsonResource
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
            'lead_id' => $this->lead_id,
            'employee_id' => $this->employee_id,
            'remarks' => $this->remarks,
            'lead_status' => $this->lead_status,
            'created_at' => $this->created_at->toIso8601String(),
            'created_by' => $this->created_by,
            'createdBy' => new EmployeeResource($this->createdBy),
            'updated_at' => $this->updated_at->toIso8601String(),
            'updated_by' => $this->updated_by,
            'updatedBy' => new EmployeeResource($this->updatedBy)
        ];
    }
}
