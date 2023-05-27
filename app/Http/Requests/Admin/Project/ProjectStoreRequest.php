<?php

namespace App\Http\Requests\Admin\Project;

use Illuminate\Foundation\Http\FormRequest;

class ProjectStoreRequest extends FormRequest
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
            'name' => 'required|string|max:100|unique:projects',
            'description' => 'required|string',
            'logo' => 'image|dimensions:min_width=50,min_height=50,max_width=2000,max_height=2000',
            'client_id' => 'required|integer|exists:clients,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'period' => 'required|string',
            'price' => 'required|integer',
            'users' => 'nullable|array',
            'mailboxes' => 'nullable|array',
            'linkedin_accounts' => 'nullable|array',
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
            'name.required' => 'Name is required!',
            'name.string' => 'Name should be a string!',
            'name.max' => 'Name should be not longer than 50 chars!',

            'description.required' => 'Description is required!',
            'description.text' => 'Description should be a text!',

            'client_id.required' => 'Client is required!',
            'client_id.exists' => 'Such client does not exist!',

            'logo.image' => 'Logo should be image!',
            'logo.dimensions' => 'Logo should have such dimensions: width[50px - 2000px], height[50px - 2000px]!',

            'start_date.required' => 'Start date is required!',
            'start_date.date' => 'Start date should have date format!',

            'end_date.required' => 'End date is required!',
            'end_date.date' => 'End date should have date format!',

            'period.required' => 'Period is required!',
            'period.string' => 'Period should be a string!',

            'price.required' => 'Price is required!',
            'price.integer' => 'Price should be an integer!',
        ];
    }
}
