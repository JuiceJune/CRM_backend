<?php

namespace App\Http\Requests\Prospect;

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
            'campaign_id' => 'required|string|exists:campaigns,uuid',
            'prospects' => 'array',
            'prospects.*.first_name' => 'nullable|string|max:100',
            'prospects.*.last_name' => 'nullable|string|max:100',
            'prospects.*.email' => 'required|string|max:100',
            'prospects.*.status' => 'nullable|string',
            'prospects.*.company' => 'nullable|string',
            'prospects.*.website' => 'nullable|string',
            'prospects.*.linkedin_url' => 'nullable|string',
            'prospects.*.date_added' => 'nullable|date',
            'prospects.*.phone' => 'nullable|string',
            'prospects.*.title' => 'nullable|string',
            'prospects.*.address' => 'nullable|string',
            'prospects.*.city' => 'nullable|string',
            'prospects.*.state' => 'nullable|string',
            'prospects.*.country' => 'nullable|string',
            'prospects.*.timezone' => 'nullable|string',
            'prospects.*.industry' => 'nullable|string',
            'prospects.*.tags' => 'nullable|array',
            'prospects.*.snippet_1' => 'nullable|string',
            'prospects.*.snippet_2' => 'nullable|string',
            'prospects.*.snippet_3' => 'nullable|string',
            'prospects.*.snippet_4' => 'nullable|string',
            'prospects.*.snippet_5' => 'nullable|string',
            'prospects.*.snippet_6' => 'nullable|string',
            'prospects.*.snippet_7' => 'nullable|string',
            'prospects.*.snippet_8' => 'nullable|string',
            'prospects.*.snippet_9' => 'nullable|string',
            'prospects.*.snippet_10' => 'nullable|string',
            'prospects.*.snippet_11' => 'nullable|string',
            'prospects.*.snippet_12' => 'nullable|string',
            'prospects.*.snippet_13' => 'nullable|string',
            'prospects.*.snippet_14' => 'nullable|string',
            'prospects.*.snippet_15' => 'nullable|string',
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

            'campaign_id.required' => 'Campaign Id is required!',
            'campaign_id.integer' => 'Campaign Id should be a string!',
            'campaign_id.exists' => 'Campaign Id already exists!',
        ];
    }
}
