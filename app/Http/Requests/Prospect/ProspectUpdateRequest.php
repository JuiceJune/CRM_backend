<?php

namespace App\Http\Requests\Prospect;

use Illuminate\Foundation\Http\FormRequest;

class ProspectUpdateRequest extends FormRequest
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
            'account_id' => 'integer|exists:accounts,id',
            'campaign_id' => 'integer|exists:campaigns,id',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'string|max:100',
            'status' => 'nullable|string',
            'company' => 'nullable|string',
            'website' => 'nullable|string',
            'linkedin_url' => 'nullable|string',
            'date_added' => 'nullable|date',
            'phone' => 'nullable|string',
            'title' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'timezone' => 'nullable|string',
            'industry' => 'nullable|string',
            'tags' => 'nullable|array',
            'snippet_1' => 'nullable|string',
            'snippet_2' => 'nullable|string',
            'snippet_3' => 'nullable|string',
            'snippet_4' => 'nullable|string',
            'snippet_5' => 'nullable|string',
            'snippet_6' => 'nullable|string',
            'snippet_7' => 'nullable|string',
            'snippet_8' => 'nullable|string',
            'snippet_9' => 'nullable|string',
            'snippet_10' => 'nullable|string',
            'snippet_11' => 'nullable|string',
            'snippet_12' => 'nullable|string',
            'snippet_13' => 'nullable|string',
            'snippet_14' => 'nullable|string',
            'snippet_15' => 'nullable|string',
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
            'first_name.required' => 'First name is required!',
            'first_name.string' => 'First name should be a string!',
            'first_name.max' => 'First name should be not longer than 50 chars!',

            'last_name.required' => 'Last name is required!',
            'last_name.string' => 'Last name should be a string!',
            'last_name.max' => 'Last name should be not longer than 50 chars!',

            'email.required' => 'Email is required!',
            'email.string' => 'Email should be a string!',
            'email.max' => 'Email should be not longer than 50 chars!',

            'campaign_id.required' => 'Campaign Id is required!',
            'campaign_id.integer' => 'Campaign Id should be a string!',
            'campaign_id.exists' => 'Campaign Id already exists!',
        ];
    }
}
