<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|min:2',
            'middle_name' => 'nullable|min:2',
            'last_name' => 'required|min:2',
            'title' => 'required|min:2',
            'alias' => 'nullable|min:2',
            'suffix' => 'nullable|min:2',
            'birth_date' => 'date_format:Y-m-d',
            'address' => 'required|min:2',
            'secondary_address' => 'nullable|min:2',
            'nationality' => 'required|min:2',
            'gender' => 'required|min:2',
            'civil_status' => 'required|min:2',
            'company_name' => 'required|min:2',
            'company_number' => 'nullable|min:2',
            'occupation' => 'required|min:2',
            'email' => 'required|email',
            'mobile_number_one' => 'required|min:2',
            'mobile_number_two' => 'nullable|min:2',
            'telephone' => 'nullable|min:2',
            'fax' => 'nullable|min:2',
            'combined_monthly_income' => 'required|min:2',
            'internet_connection' => 'required|min:2',
            'owned_gadgets' => 'required|array',
            'other_gadgets' => 'nullable|min:2',
            'spouse_occupation' => 'required|min:2',
            'nature_of_business' => 'required|min:2',
            'property_id' => 'required|exists:properties,id',
            'contract_file' => 'nullable|file|mimes:png,jpg,jpeg',
            'source_id' => 'required|exists:sources,id',
            'presentation_date' => 'required|date_format:Y-m-d',
            'refer_by' => 'nullable|min:2',
            'holiday_consultant' => 'nullable|min:2',
            'membership_type' => 'nullable|min:2',
            'venue_id' => 'required|exists:venues,id'
        ];
    }
}
