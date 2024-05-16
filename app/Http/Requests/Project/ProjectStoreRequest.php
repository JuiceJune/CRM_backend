<?php

namespace App\Http\Requests\Project;

use App\Rules\UniquePerAccount;
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
            'name' => ['required', 'string', 'max:50', new UniquePerAccount('projects', 'name', $this->user()->account_id)],
            'client_id' => 'required|integer|exists:clients,id',
            'logo' => 'image',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'users' => 'nullable|array',
            'creator_id' => 'required|integer|exists:users,id',
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
            'name.required' => 'Name is required!',
            'name.string' => 'Name should be a string!',
            'name.max' => 'Name should be not longer than 50 chars!',
            'name.unique' => 'Project with this name already exists!',

            'logo.image' => 'Logo should be image!',

            'client_id.required' => 'Client is required!',
            'client_id.exists' => 'Such client does not exist!',

            'start_date.date' => 'Start date should have date format!',

            'end_date.date' => 'End date should have date format!',
        ];
    }
}
