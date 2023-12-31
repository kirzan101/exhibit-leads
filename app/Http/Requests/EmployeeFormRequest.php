<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeFormRequest extends FormRequest
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
            'position' => 'required|min:2',
            'property_id' => 'required|exists:properties,id',
            'email' => 'required|email|unique:users,email,'.$this->user_id,
            'user_group_id' => 'required|exists:user_groups,id',
            'venue_ids' => 'nullable|array',
            'exhibitor_id' => 'required_if:user_group_id,3' //employee user group
        ];
    }

    /**
     * modify error messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'exhibitor_id.required_if' => 'Exhibitor field is required if user group is employees.'
        ];
    }
}
