<?php

namespace App\Http\Requests\Admin\EmailProvider;

use Illuminate\Foundation\Http\FormRequest;

class EmailProviderStoreRequest extends FormRequest
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
            'title' => 'required|string|unique:email_providers|max:50',
            'logo' => 'nullable|image|dimensions:min_width=50,min_height=50,max_width=2000,max_height=2000'
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
            'title.required' => 'Title is required!',
            'title.unique' => 'Such email provider already exists!',
            'title.max' => 'Email provider title should be not longer than 50 chars!',
            'title.string' => 'Title should be a string!',
            'logo.image' => 'Logo should be image!',
            'logo.dimensions' => 'Logo should have such dimensions: width[50px - 2000px], height[50px - 2000px]!',
        ];
    }
}
