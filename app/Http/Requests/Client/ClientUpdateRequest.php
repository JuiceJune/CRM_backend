<?php

namespace App\Http\Requests\Client;

use App\Rules\UniquePerAccount;
use Illuminate\Foundation\Http\FormRequest;

class ClientUpdateRequest extends FormRequest
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
        $client = $this->route('client');

        return [
            'name' => ['required', 'string', 'max:100', new UniquePerAccount('clients', 'name', $client->account_id, $client->id)],
            'email' => ['required', 'string', 'email', 'max:255', new UniquePerAccount('clients', 'email', $client->account_id, $client->id)],
            'avatar' => 'image',
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
            'name.unique' => 'Client with this name already exists!',

            'email.required' => 'Email is required!',
            'email.string' => 'Email should be a string!',
            'email.unique' => 'Client with this email already exists!',

            'avatar.image' => 'Avatar should be image!',
        ];
    }
}
