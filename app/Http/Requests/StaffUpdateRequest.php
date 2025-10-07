<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffUpdateRequest extends FormRequest
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
        $staffId = $this->route('staff')->id ?? $this->route('staff');
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2'
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($staffId)
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed'
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'min:10'
            ],
            'address' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'store_id' => [
                'required',
                'exists:stores,id'
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048' // 2MB max
            ]
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Staff name is required.',
            'name.string' => 'Staff name must be a valid string.',
            'name.max' => 'Staff name cannot exceed 255 characters.',
            'name.min' => 'Staff name must be at least 2 characters long.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'email.unique' => 'This email address is already registered.',
            'password.string' => 'Password must be a valid string.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'phone.string' => 'Phone number must be a valid string.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'phone.min' => 'Phone number must be at least 10 characters long.',
            'address.string' => 'Address must be a valid string.',
            'address.max' => 'Address cannot exceed 1000 characters.',
            'store_id.required' => 'Store selection is required.',
            'store_id.exists' => 'Selected store does not exist.',
            'avatar.image' => 'Avatar must be an image file.',
            'avatar.mimes' => 'Avatar must be a JPEG, PNG, JPG, or GIF file.',
            'avatar.max' => 'Avatar file size cannot exceed 2MB.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'staff name',
            'email' => 'email address',
            'password' => 'password',
            'phone' => 'phone number',
            'address' => 'address',
            'store_id' => 'store',
            'avatar' => 'profile image'
        ];
    }
}