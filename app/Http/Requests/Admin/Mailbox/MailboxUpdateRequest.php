<?php

namespace App\Http\Requests\Admin\Mailbox;

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
     */
    public function rules(): array
    {
        return[
            "email" => "required|string|unique:mailboxes,email," . $this->mailbox_id,
            "name" => "required|string|max:50",
            "domain" => "required|string",
            "avatar" => "nullable",
            "phone" => "nullable|string|max:20",
            "password" => "nullable|string|max:50",
            "app_password" => "nullable|string",
            "email_provider" => "nullable|integer",
            "token" => "string",
            "refresh_token" => "string",
            "expires_in" => "string",
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

            'phone.string' => 'Phone should be a string!',
            'phone.max' => 'Phone should be not longer than 20 chars!',

            'email.required' => 'Email is required!',
            'email.unique' => 'Such email already exists!',
            'email.string' => 'Email should be a string!',

            'domain.required' => 'Domain is required!',
            'domain.string' => 'Domain should be a string!',

            'password.required' => 'Password is required!',
            'password.string' => 'Password should be a string!',
            'password.max' => 'Password should be not longer than 50 chars!',

            'create_date.required' => 'Create date is required!',
            'create_date.date' => 'Create date should be a date!',

            'app_password.string' => 'App password should be a string!',

            'email_provider_id.required' => 'Email provider is required!',
            'email_provider_id.integer' => 'email_provider_id should be an integer !',
            'email_provider_id.exists' => 'email_provider_id should be exists!',
        ];
    }
}
