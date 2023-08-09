<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
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
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'title' => $this->title,
            'alias' => $this->alias,
            'suffix' => $this->suffix,
            'birth_date' => $this->birth_date,
            'address' => $this->address,
            'secondary_address' => $this->secondary_address,
            'nationality' => $this->nationality,
            'gender' => $this->gender,
            'civil_status' => $this->civil_status,
            'company_name' => $this->company_name,
            'company_number' => $this->company_number,
            'occupation' => $this->occupation,
            'email' => $this->email,
            'mobile_number_one' => $this->mobile_number_one,
            'mobile_number_two' => $this->mobile_number_two,
            'mobile_number' => $this->getMobileNumber(),
            'telephone' => $this->telephone,
            'fax' => $this->fax,
            'combined_monthly_income' => $this->combined_monthly_income,
            'internet_connection' => $this->internet_connection,
            'owned_gadgets' => $this->owned_gadgets,
            'other_gadgets' => $this->other_gadgets,
            'spouse_occupation' => $this->spouse_occupation,
            'property_id' => $this->property_id,
            'property' => $this->property,
            'contract_file_name' => $this->getFileName(),
            'uploaded_contract_file' => ($this->contract_file) ? asset($this->contract_file) : null, //($this->contract_file) ? Storage::disk('public')->get($this->contract_file) : null,
            'is_booker_assigned' => (bool) $this->is_booker_assigned,
            'is_done' => (bool) $this->is_done,
            'is_done_confirmed' => (bool) $this->is_done_confirmed,
            'is_confirm_assigned' => (bool) $this->is_confirm_assigned,
            'is_showed' => (bool) $this->is_showed,
            'is_exhibitor_assigned' => (bool) $this->is_exhibitor_assigned,
            'remarks' => $this->remarks,
            'lead_full_name' => $this->getFullName(),
            'assigned_confirmer_name' => $this->getAssignedConfirmer(),
            'assigned_confirmer' => $this->assignedConfirmer,
            'assigned_employee' => new AssignedEmployeeResource($this->assignedEmployee),
            'assigned_employee_name' => $this->getAssignedEmployee(),
            'assigned_exhibitor' => $this->assignedExhibitor,
            'assigned_exhibitor_name' => $this->getAssignedExhibitor(),
            'presentation_date' => $this->presentation_date,
            'exhibit_code' => $this->exhibit_code,
            'source_complete' => $this->getSource(),
            'source' => $this->source,
            'source_prefix' => $this->source_prefix,
            'refer_by' => $this->refer_by,
            'holiday_consultant' => $this->holiday_consultant,
            'membership_type' => $this->membership_type,
            'is_confidential' => (bool) $this->is_confidential,
            'venue' => $this->venue,
            'venue_id' => $this->venue_id,
            'lead_status' => $this->lead_status,
            'lead_status_confirmer' => $this->lead_status_confirmer,
            'created_at' => $this->created_at,
            'created_by' => $this->createdBy,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updatedBy
        ];
    }
}
