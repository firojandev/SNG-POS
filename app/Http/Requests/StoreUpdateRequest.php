<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreUpdateRequest extends FormRequest
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
        // Get store ID from route parameter or from authenticated user (for my-store route)
        $storeId = $this->route('store')
            ? ($this->route('store')->id ?? $this->route('store'))
            : Auth::user()->store_id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                Rule::unique('stores', 'name')
                    ->ignore($storeId)
                    ->whereNull('deleted_at')
            ],
            'contact_person' => [
                'required',
                'string',
                'max:255',
                'min:2'
            ],
            'phone_number' => [
                'required',
                'string',
                'max:20',
                'min:10'
            ],
            'address' => [
                'required',
                'string',
                'max:1000',
                'min:10'
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('stores', 'email')
                    ->ignore($storeId)
                    ->whereNull('deleted_at')
            ],
            'details' => [
                'nullable',
                'string',
                'max:2000'
            ],
            'is_active' => [
                'sometimes',
                'boolean'
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
            'name.required' => 'Store name is required.',
            'name.string' => 'Store name must be a valid string.',
            'name.max' => 'Store name cannot exceed 255 characters.',
            'name.min' => 'Store name must be at least 2 characters long.',
            'name.unique' => 'This store name already exists.',
            'contact_person.required' => 'Contact person is required.',
            'contact_person.string' => 'Contact person must be a valid string.',
            'contact_person.max' => 'Contact person cannot exceed 255 characters.',
            'contact_person.min' => 'Contact person must be at least 2 characters long.',
            'phone_number.required' => 'Phone number is required.',
            'phone_number.string' => 'Phone number must be a valid string.',
            'phone_number.max' => 'Phone number cannot exceed 20 characters.',
            'phone_number.min' => 'Phone number must be at least 10 characters long.',
            'address.required' => 'Address is required.',
            'address.string' => 'Address must be a valid string.',
            'address.max' => 'Address cannot exceed 1000 characters.',
            'address.min' => 'Address must be at least 10 characters long.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'email.unique' => 'This email address already exists.',
            'details.string' => 'Details must be a valid string.',
            'details.max' => 'Details cannot exceed 2000 characters.',
            'is_active.boolean' => 'Active status must be true or false.'
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
            'name' => 'store name',
            'contact_person' => 'contact person',
            'phone_number' => 'phone number',
            'address' => 'address',
            'email' => 'email',
            'details' => 'details',
            'is_active' => 'active status'
        ];
    }
}
