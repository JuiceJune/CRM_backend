<?php

namespace App\Http\Requests\Campaign;

use App\Rules\UniquePerAccount;
use Illuminate\Foundation\Http\FormRequest;

class CampaignUpdateRequest extends FormRequest
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
        $campaign = $this->route('campaign');
        return [
            'name' => ['sometimes', 'string', 'max:100', new UniquePerAccount('campaigns', 'name', $campaign->account_id, $campaign->id, $campaign->projecct_id)],
            'mailbox_id' => 'nullable|integer|exists:mailboxes,id,account_id,' . $campaign->account_id,
            'status' => 'sometimes|string',
            'timezone' => 'sometimes|string',
            'start_date' => 'sometimes|date',
            'send_limit' => 'sometimes|integer',
            'steps' => 'sometimes|array|min:1',
            'steps.*.id' => 'sometimes|string',
            'steps.*.step' => 'sometimes|integer',
            'steps.*.period' => 'sometimes|integer|min:60',
            'steps.*.start_after' => 'sometimes',
            'steps.*.sending_time_json' => 'sometimes',
            'steps.*.reply_to_exist_thread' => 'sometimes',
            'steps.*.versions' => 'sometimes|array|min:1',
            'steps.*.versions.*.id' => 'sometimes|string',
            'steps.*.versions.*.subject' => 'sometimes|string',
            'steps.*.versions.*.message' => 'sometimes|string',
            'steps.*.versions.*.version' => 'sometimes|string',
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
