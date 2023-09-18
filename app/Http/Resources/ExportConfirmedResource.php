<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ExportConfirmedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'lead_name' => $this->getFullName(),
            'occupation' => $this->occupation,
            'venue' => $this->venue->name,
            'source' => $this->getSource(),
            'booker' => $this->getAssignedEmployee(),
            'exhibitor' => $this->getAssignedExhibitor(),
            'confirmer' => $this->getAssignedConfirmer(),
            'done_at' => Carbon::parse($this->assignedConfirmer->updated_at)->format('Y-m-d H:i A')
        ];
    }
}
