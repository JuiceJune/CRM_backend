<?php

namespace App\Http\Requests\Admin\Mailbox;

use Illuminate\Foundation\Http\FormRequest;

class MailboxStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "email" => "required|string|unique:mailboxes",
            "name" => "required|string|max:50",
            "domain" => "required|string",
            "avatar" => "nullable",
            "phone" => "nullable|string|max:20",
            "password" => "nullable|string|max:50",
            "app_password" => "nullable|string",
            "token" => "string",
            "refresh_token" => "string",
            "expires_in" => "string",
            "signature" => "string"
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
            'email.required' => 'Email is required!',
            'email.unique' => 'Such email already exists!',
            'email.string' => 'Email should be a string!',

            'name.required' => 'Name is required!',
            'name.string' => 'Name should be a string!',
            'name.max' => 'Name should be not longer than 50 chars!',

            'domain.required' => 'Domain is required!',
            'domain.string' => 'Domain should be a string!',

            'phone.string' => 'Phone should be a string!',
            'phone.max' => 'Phone should be not longer than 20 chars!',

            'password.string' => 'Password should be a string!',
            'password.max' => 'Password should be not longer than 50 chars!',

            'app_password.string' => 'App password should be a string!',

            'email_provider.integer' => 'email_provider_id should be an integer !',
            'email_provider.exists' => 'email_provider_id should be exists!',
        ];
    }
}
