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
            'birth_date' => 'nullable|date_format:Y-m-d',
            'age' => 'nullable|numeric',
            'address' => 'required|min:2',
            'secondary_address' => 'nullable|min:2',
            'nationality' => 'nullable|min:2',
            'gender' => 'nullable|min:2',
            'civil_status' => 'nullable|min:2',
            'company_name' => 'nullable|min:2',
            'company_number' => 'nullable|min:2',
            'occupation' => 'required|min:2',
            'email' => 'nullable|email',
            'mobile_number_one' => 'required|min:2',
            'mobile_number_two' => 'nullable|min:2',
            'telephone' => 'nullable|min:2',
            'fax' => 'nullable|min:2',
            'combined_monthly_income' => 'nullable|min:2',
            'internet_connection' => 'nullable|min:2',
            'other_gadgets' => 'nullable|min:2',
            'spouse_occupation' => 'nullable|min:2',
            'nature_of_business' => 'nullable|min:2',
            'property_id' => 'nullable|integer',//'required|exists:properties,id',
            'contract_file' => 'nullable|file|mimes:png,jpg,jpeg',
            'source_prefix' => 'required|min:2',
            'source' => 'required_with:source_prefix|min:2',
            'presentation_date' => 'nullable|date_format:Y-m-d',
            'refer_by' => 'nullable|min:2',
            'holiday_consultant' => 'nullable|min:2',
            'membership_type' => 'nullable|min:2',
            'venue_id' => 'nullable|exists:venues,id',
            'spouse_first_name' => 'nullable|min:2',
            'spouse_last_name' => 'nullable|min:2',
            'date_filled' => 'required|date_format:Y-m-d',
            'stab_number' => 'nullable|regex:/^[0-9]+$/|max:6|unique:leads,stab_number,'.$this->id,
            'exhibitor' => 'nullable|min:2',
        ];
    }

    public function messages() : array
    {
        return [
            'spouse_first_name.min' => 'Spouse/Partner first name must be at least 2 characters.',
            'spouse_last_name.min' => 'Spouse/Partner last name must be at least 2 characters.'
        ];
    }
}
