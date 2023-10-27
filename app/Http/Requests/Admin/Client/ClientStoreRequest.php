<?php

namespace App\Http\Requests\Admin\Client;

use Illuminate\Foundation\Http\FormRequest;

class ClientStoreRequest extends FormRequest
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
            'avatar' => 'image',
            'name' => 'required|string|max:100',
            'email' => 'required|string|unique:users',
            'location' => 'required|string',
            'industry' => 'required|string',
            'start_date' => 'required|date',
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
            'avatar.image' => 'Avatar should be image!',

            'name.required' => 'Name is required!',
            'name.string' => 'Name should be a string!',
            'name.max' => 'Name should be not longer than 50 chars!',

            'email.required' => 'Email is required!',
            'email.string' => 'Email should be a string!',
            'email.unique' => 'This email is already in use!',

            'location.required' => 'Location is required!',
            'location.string' => 'Location should be a string!',

            'industry.required' => 'Industry is required!',
            'industry.string' => 'Industry should be a string!',

            'start_date.required' => 'Start date is required!',
            'start_date.date' => 'Start date should have date format!',
        ];
    }
}
