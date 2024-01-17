<?php

namespace App\Http\Requests\Admin\Prospect;

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
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|max:100',
            'status' => 'string',
            'campaign_id' => 'required|integer|exists:campaigns,id',
            'company' => 'string',
            'website' => 'string',
            'linkedin_url' => 'string',
            'date_contacted' => 'date',
            'date_responded' => 'date',
            'date_added' => 'date',
            'phone' => 'string',
            'title' => 'string',
            'address' => 'string',
            'city' => 'string',
            'state' => 'string',
            'country' => 'string',
            'timezone' => 'string',
            'industry' => 'string',
            'tags' => 'array',
            'snippet_1' => 'string',
            'snippet_2' => 'string',
            'snippet_3' => 'string',
            'snippet_4' => 'string',
            'snippet_5' => 'string',
            'snippet_6' => 'string',
            'snippet_7' => 'string',
            'snippet_8' => 'string',
            'snippet_9' => 'string',
            'snippet_10' => 'string',
            'snippet_11' => 'string',
            'snippet_12' => 'string',
            'snippet_13' => 'string',
            'snippet_14' => 'string',
            'snippet_15' => 'string',
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
