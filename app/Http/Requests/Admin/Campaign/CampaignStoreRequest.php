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
            'status' => 'string',
            'timezone' => 'string',
            'start_date' => 'date',
            'send_limit' => 'integer',
            'priority_config' => 'required',
            'steps' => 'required|array|min:1',
            'steps.*.period' => 'required|integer|min:60',
            'steps.*.start_after' => 'required',
            'steps.*.sending_time_json' => 'required',
            'steps.*.step' => 'required|integer',
            'steps.*.versions' => 'required|array|min:1',
            'steps.*.versions.*.subject' => 'required|string',
            'steps.*.versions.*.message' => 'required|string',
            'steps.*.versions.*.version' => 'required|string',
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
