<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VatStoreRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                Rule::unique('vats', 'name')->whereNull('deleted_at')
            ],
            'value' => [
                'required',
                'numeric',
                'min:0',
                'max:999.99'
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
            'name.required' => 'VAT name is required.',
            'name.string' => 'VAT name must be a valid string.',
            'name.max' => 'VAT name cannot exceed 255 characters.',
            'name.min' => 'VAT name must be at least 2 characters long.',
            'name.unique' => 'This VAT name already exists.',
            'value.required' => 'VAT value is required.',
            'value.numeric' => 'VAT value must be a valid number.',
            'value.min' => 'VAT value cannot be negative.',
            'value.max' => 'VAT value cannot exceed 999.99.'
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
            'name' => 'VAT name',
            'value' => 'VAT value'
        ];
    }
}
