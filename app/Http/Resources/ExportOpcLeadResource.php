<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExportOpcLeadResource extends JsonResource
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
            'companion_name' => $this->getCompanionFullName(),
            'address' => $this->address,
            'hotel' => $this->hotel,
            'mobile_number' => $this->mobile_number,
            'occupation' => $this->occupation,
            'age' => $this->age,
            'source' => $this->getSource(),
            'civil_status' => $this->civil_status,
            'date_filled' => Carbon::parse($this->date_filled)->format('Y-m-d H:i A'),
            'date_uploaded' => Carbon::parse($this->created_at)->format('Y-m-d H:i A')
        ];
    }
}
