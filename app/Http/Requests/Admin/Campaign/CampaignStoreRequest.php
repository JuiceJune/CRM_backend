<?php

namespace App\Http\Requests\Admin\Campaign;

use Illuminate\Foundation\Http\FormRequest;

class CampaignStoreRequest extends FormRequest
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
            'name' => 'required|string|max:100',
            'mailbox_id' => 'required|integer|exists:mailboxes,id',
            'project_id' => 'required|integer|exists:projects,id',
            'subject' => 'required|string',
            'message' => 'required',
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

            'mailbox_id.required' => 'Mailbox Id is required!',
            'mailbox_id.integer' => 'Mailbox Id should be a string!',
            'mailbox_id.exists' => 'Mailbox Id already exists!',

            'project_id.required' => 'Project Id is required!',
            'project_id.integer' => 'Project Id should be a string!',
            'project_id.exists' => 'Project Id already exists!',
        ];
    }
}
