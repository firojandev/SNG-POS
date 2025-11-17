<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaxUpdateRequest extends FormRequest
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
        $taxId = $this->route('tax')->id ?? $this->route('tax');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                Rule::unique('taxes', 'name')
                    ->where('store_id', Auth::user()->store_id)
                    ->ignore($taxId)
                    ->whereNull('deleted_at')
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
            'name.required' => 'Tax name is required.',
            'name.string' => 'Tax name must be a valid string.',
            'name.max' => 'Tax name cannot exceed 255 characters.',
            'name.min' => 'Tax name must be at least 2 characters long.',
            'name.unique' => 'This tax name already exists.',
            'value.required' => 'Tax value is required.',
            'value.numeric' => 'Tax value must be a valid number.',
            'value.min' => 'Tax value cannot be negative.',
            'value.max' => 'Tax value cannot exceed 999.99.'
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
            'name' => 'tax name',
            'value' => 'tax value'
        ];
    }
}
