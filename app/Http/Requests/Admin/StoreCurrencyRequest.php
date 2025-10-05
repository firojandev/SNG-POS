<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurrencyRequest extends FormRequest
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
            'name' => 'required|string|max:10|unique:currencies,name',
            'symbol' => 'required|string|max:5',
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
            'name.required' => 'Currency name is required.',
            'name.max' => 'Currency name cannot exceed 10 characters.',
            'name.unique' => 'This currency already exists.',
            'symbol.required' => 'Currency symbol is required.',
            'symbol.max' => 'Currency symbol cannot exceed 5 characters.',
        ];
    }
}
