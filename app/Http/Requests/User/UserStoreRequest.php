<?php

namespace App\Http\Requests\User;

use App\Rules\UniquePerAccount;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', new UniquePerAccount('users', 'name', $this->user()->account_id)],
            'email' => ['required', 'string', 'email', 'max:100', new UniquePerAccount('users', 'email', $this->user()->account_id)],
            'password' => 'required|string|max:50',
            'avatar' => 'image',
            'role_id' => 'required|integer|exists:roles,id',
            'position_id' => 'required|integer|exists:positions,id',
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

            'password.required' => 'Password is required!',
            'password.string' => 'Password should be a string!',
            'password.max' => 'Password should be not longer than 50 chars!',

            'role_id.required' => 'Role is required!',
            'role_id.exists' => 'Such role does not exist!',

            'position_id.required' => 'Role is required!',
            'position_id.exists' => 'Such role does not exist!',

            'avatar.image' => 'Avatar should be image!',
        ];
    }
}
