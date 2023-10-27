<?php

namespace App\Http\Requests\Admin\Project;

use Illuminate\Foundation\Http\FormRequest;

class ProjectUpdateRequest extends FormRequest
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
            'logo' => 'image',
            'name' => 'required|string|max:100|unique:projects,name,' . $this->project_id,
            'client_id' => 'required|integer|exists:clients,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'price' => 'required|integer',
            'users' => 'nullable|array',
            'mailboxes' => 'nullable|array',
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
            'logo.image' => 'Logo should be image!',

            'name.required' => 'Name is required!',
            'name.string' => 'Name should be a string!',
            'name.max' => 'Name should be not longer than 50 chars!',

            'client_id.required' => 'Client is required!',
            'client_id.exists' => 'Such client does not exist!',

            'start_date.required' => 'Start date is required!',
            'start_date.date' => 'Start date should have date format!',

            'end_date.required' => 'End date is required!',
            'end_date.date' => 'End date should have date format!',

            'price.required' => 'Price is required!',
            'price.integer' => 'Price should be an integer!',
        ];
    }
}
