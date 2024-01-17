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
            'prospects.*.status' => 'string',
            'campaign_id' => 'required|integer|exists:campaigns,id',
            'prospects.*.company' => 'string',
            'prospects.*.website' => 'string',
            'prospects.*.linkedin_url' => 'string',
            'prospects.*.date_contacted' => 'date',
            'prospects.*.date_responded' => 'date',
            'prospects.*.date_added' => 'date',
            'prospects.*.phone' => 'string',
            'prospects.*.title' => 'string',
            'prospects.*.address' => 'string',
            'prospects.*.city' => 'string',
            'prospects.*.state' => 'string',
            'prospects.*.country' => 'string',
            'prospects.*.timezone' => 'string',
            'prospects.*.industry' => 'string',
            'prospects.*.tags' => 'array',
            'prospects.*.snippet_1' => 'string',
            'prospects.*.snippet_2' => 'string',
            'prospects.*.snippet_3' => 'string',
            'prospects.*.snippet_4' => 'string',
            'prospects.*.snippet_5' => 'string',
            'prospects.*.snippet_6' => 'string',
            'prospects.*.snippet_7' => 'string',
            'prospects.*.snippet_8' => 'string',
            'prospects.*.snippet_9' => 'string',
            'prospects.*.snippet_10' => 'string',
            'prospects.*.snippet_11' => 'string',
            'prospects.*.snippet_12' => 'string',
            'prospects.*.snippet_13' => 'string',
            'prospects.*.snippet_14' => 'string',
            'prospects.*.snippet_15' => 'string',
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
