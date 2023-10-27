<?php

namespace App\Http\Requests\Admin\Prospect;

use Illuminate\Foundation\Http\FormRequest;

class ProspectsStoreRequest extends FormRequest
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
            'prospects' => 'array',
            'prospects.*.first_name' => 'required|string|max:100',
            'prospects.*.last_name' => 'required|string|max:100',
            'prospects.*.email' => 'required|string|max:100',
            'prospects.*.phone' => 'string',
            'prospects.*.location' => 'string',
            'prospects.*.status' => 'string',
            'prospects.*.campaign_id' => 'required|integer|exists:campaigns,id',
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'prospects.*.first_name.required' => 'First name is required!',
            'prospects.*.first_name.string' => 'First name should be a string!',
            'prospects.*.first_name.max' => 'First name should be not longer than 50 chars!',

            'prospects.*.last_name.required' => 'Last name is required!',
            'prospects.*.last_name.string' => 'Last name should be a string!',
            'prospects.*.last_name.max' => 'Last name should be not longer than 50 chars!',

            'prospects.*.email.required' => 'Email is required!',
            'prospects.*.email.string' => 'Email should be a string!',
            'prospects.*.email.max' => 'Email should be not longer than 50 chars!',

            'prospects.*.campaign_id.required' => 'Campaign Id is required!',
            'prospects.*.campaign_id.integer' => 'Campaign Id should be a string!',
            'prospects.*.campaign_id.exists' => 'Campaign Id already exists!',
        ];
    }
}
