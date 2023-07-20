<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MemberResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = null;

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
            'telephone' => $this->telephone,
            'fax' => $this->fax,
            'combined_monthly_income' => $this->combined_monthly_income,
            'internet_connection' => $this->internet_connection,
            'owned_gadgets' => $this->owned_gadgets,
            'other_gadgets' => $this->other_gadgets,
            'spouse_occupation' => $this->spouse_occupation,
            'property_id' => $this->property_id,
            'contract_file_name' => $this->getFileName(),
            'uploaded_contract_file' => ($this->contract_file) ? asset($this->contract_file) : null, //($this->contract_file) ? Storage::disk('public')->get($this->contract_file) : null,
            'is_assigned' => (bool) $this->is_assigned,
            'remarks' => $this->remarks,
            'employee' => $this->employee,
            'employee_full_name' => ($this->employee_id) ? $this->employee->getFullName() : null,
            'member_full_name' => $this->getFullName(),
            'created_at' => $this->created_at,
            'created_by' => $this->createdBy,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updatedBy
        ];
    }
}
