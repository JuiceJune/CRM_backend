<?php

namespace App\Http\Requests\Mailbox;

use App\Rules\UniquePerAccount;
use Illuminate\Foundation\Http\FormRequest;

class MailboxUpdateRequest extends FormRequest
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
        $mailbox = $this->route('mailbox');

        return[
            'name' => 'required|string|max:100',
            'email' => ['required', 'string', 'email', 'max:255', new UniquePerAccount('mailboxes', 'email', $mailbox->account_id, $mailbox->id)],
            "send_limit" => "integer",
            "signature" => "string",
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
            'name.max' => 'Name should be not longer than 100 chars!',

            'email.required' => 'Email is required!',
            'email.string' => 'Email should be a string!',
            'email.unique' => 'This email is already in use!',

            'send_limit.integer' => 'Send Limit should be an integer!',

            'signature.string' => 'Signature should be a text!',

            'email_provider.string' => 'Email provider should be a text!',
        ];
    }
}
