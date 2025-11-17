<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AssetUpdateRequest extends FormRequest
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
        $assetId = $this->route('asset')->id ?? $this->route('asset');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                Rule::unique('assets', 'name')
                    ->ignore($assetId)
                    ->where('store_id', Auth::user()->store_id)
                    ->whereNull('deleted_at')
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999.99'
            ],
            'note' => [
                'nullable',
                'string',
                'max:1000'
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
            'name.required' => 'Asset name is required.',
            'name.string' => 'Asset name must be a valid string.',
            'name.max' => 'Asset name cannot exceed 255 characters.',
            'name.min' => 'Asset name must be at least 2 characters long.',
            'name.unique' => 'This asset name already exists.',
            'amount.required' => 'Asset amount is required.',
            'amount.numeric' => 'Asset amount must be a valid number.',
            'amount.min' => 'Asset amount cannot be negative.',
            'amount.max' => 'Asset amount is too large.',
            'note.string' => 'Note must be a valid string.',
            'note.max' => 'Note cannot exceed 1000 characters.'
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
            'name' => 'asset name',
            'amount' => 'asset amount',
            'note' => 'note'
        ];
    }
}
