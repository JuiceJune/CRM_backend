<?php

namespace App\Http\Requests\Admin\Linkedin;

use Illuminate\Foundation\Http\FormRequest;

class LinkedinUpdateRequest extends FormRequest
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
            "name" => "required|string|max:50",
            "link" => "required|string",
            "warmup" => "nullable|string",
            "avatar" => "image|dimensions:min_width=50,min_height=50,max_width=2000,max_height=2000",
            "password" => "required|string|max:50",
            "create_date" => "required|date",
            "mailbox_id" => "required|integer|exists:mailboxes,id|unique:linkedin_accounts,mailbox_id," . $this->linkedin_id,
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

            'link.required' => 'Link is required!',
            'link.string' => 'Link should be a string!',

            'warmup.string' => 'Warmup should be a string!',

            'avatar.image' => 'Avatar should be image!',
            'avatar.dimensions' => 'Avatar should have such dimensions: width[50px - 2000px], height[50px - 2000px]!',

            'password.required' => 'Password is required!',
            'password.string' => 'Password should be a string!',
            'password.max' => 'Password should be not longer than 50 chars!',

            'create_date.required' => 'Create date is required!',
            'create_date.date' => 'Create date should be a date!',

            'mailbox_id.required' => 'Mailbox is required!',
            'mailbox_id.integer' => 'Mailbox id should be an integer !',
            'mailbox_id.exists' => 'Mailbox should be exists!',
            'mailbox_id.unique' => 'Mailbox already in use!',
        ];
    }
}
