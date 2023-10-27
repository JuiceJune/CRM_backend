<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
        return[
            'user_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|max:100',
            'email' => 'required|string|unique:users,email,' . $this->user_id,
            'password' => 'required|string',
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
            'name.max' => 'Name should be not longer than 50 chars!',

            'email.required' => 'Email is required!',
            'email.string' => 'Email should be a string!',
            'email.unique' => 'This email is already in use!',

            'role_id.required' => 'Role is required!',
            'role_id.exists' => 'Such role does not exist!',

            'position_id.required' => 'Role is required!',
            'position_id.exists' => 'Such role does not exist!',

            'password.required' => 'Password is required!',
            'password.string' => 'Password should be a string!',
            'password.max' => 'Password should be not longer than 50 chars!',

            'avatar.image' => 'Avatar should be image!',
        ];
    }
}
