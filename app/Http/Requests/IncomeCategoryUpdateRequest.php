<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class IncomeCategoryUpdateRequest extends FormRequest
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
        $incomeCategoryId = $this->route('income_category');
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                Rule::unique('income_categories', 'name')
                    ->ignore($incomeCategoryId)
                    ->where('store_id', Auth::user()->store_id)
                    ->whereNull('deleted_at')
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
            'name.required' => 'Income category name is required.',
            'name.string' => 'Income category name must be a valid string.',
            'name.max' => 'Income category name cannot exceed 255 characters.',
            'name.min' => 'Income category name must be at least 2 characters long.',
            'name.unique' => 'This income category name already exists.'
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
            'name' => 'income category name'
        ];
    }
}