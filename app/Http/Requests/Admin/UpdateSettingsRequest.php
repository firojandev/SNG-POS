<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
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
            'app_name' => 'required|string|max:255',
            'app_address' => 'nullable|string',
            'app_phone' => 'nullable|string|max:20',
            'date_format' => 'required|string',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'app_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:1024',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'app_name.required' => 'Application name is required.',
            'app_name.max' => 'Application name cannot exceed 255 characters.',
            'app_phone.max' => 'Phone number cannot exceed 20 characters.',
            'date_format.required' => 'Date format is required.',
            'app_logo.image' => 'Logo must be an image file.',
            'app_logo.mimes' => 'Logo must be a file of type: jpeg, png, jpg, gif.',
            'app_logo.max' => 'Logo size cannot exceed 2MB.',
            'app_favicon.image' => 'Favicon must be an image file.',
            'app_favicon.mimes' => 'Favicon must be a file of type: jpeg, png, jpg, gif, ico.',
            'app_favicon.max' => 'Favicon size cannot exceed 1MB.',
        ];
    }
}
