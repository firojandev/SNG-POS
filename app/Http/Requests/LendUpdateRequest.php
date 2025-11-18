<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LendUpdateRequest extends FormRequest
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
            'borrower' => [
                'required',
                'string',
                'max:255',
                'min:2'
            ],
            'date' => [
                'required',
                'date'
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
            ],
            'status' => [
                'required',
                'in:Due,Received'
            ]
        ];
    }

    protected function prepareForValidation(): void
    {
        $date = $this->input('date');
        if ($date) {
            $phpFormat = get_option('date_format', 'Y-m-d');
            try {
                $normalized = \Carbon\Carbon::createFromFormat($phpFormat, $date)->format('Y-m-d');
                $this->merge(['date' => $normalized]);
            } catch (\Exception $e) {
                // leave as is; validation will catch invalid date
            }
        }
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'borrower.required' => 'Borrower name is required.',
            'borrower.string' => 'Borrower name must be a valid string.',
            'borrower.max' => 'Borrower name cannot exceed 255 characters.',
            'borrower.min' => 'Borrower name must be at least 2 characters long.',
            'date.required' => 'Date is required.',
            'date.date' => 'Date must be a valid date.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount cannot be negative.',
            'amount.max' => 'Amount is too large.',
            'note.string' => 'Note must be a valid string.',
            'note.max' => 'Note cannot exceed 1000 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either Due or Received.'
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
            'borrower' => 'borrower name',
            'date' => 'date',
            'amount' => 'amount',
            'note' => 'note',
            'status' => 'status'
        ];
    }
}
