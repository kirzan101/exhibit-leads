<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'contract_file' => $this->contract_file,
            'is_assigned' => (bool) $this->is_assigned,
            'remarks' => $this->remarks,
            'employee' => $this->employee,
            'employee_full_name' => ($this->employee_id) ? $this->employee->getFullName() : null,
            'member_full_name' => $this->getFullName(),
        ];
    }
}