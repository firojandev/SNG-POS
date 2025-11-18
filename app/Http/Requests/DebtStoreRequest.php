<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DebtStoreRequest extends FormRequest
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
            'lender' => [
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
                'in:Paid,Unpaid'
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
            'lender.required' => 'Lender name is required.',
            'lender.string' => 'Lender name must be a valid string.',
            'lender.max' => 'Lender name cannot exceed 255 characters.',
            'lender.min' => 'Lender name must be at least 2 characters long.',
            'date.required' => 'Date is required.',
            'date.date' => 'Date must be a valid date.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount cannot be negative.',
            'amount.max' => 'Amount is too large.',
            'note.string' => 'Note must be a valid string.',
            'note.max' => 'Note cannot exceed 1000 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either Paid or Unpaid.'
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
            'lender' => 'lender name',
            'date' => 'date',
            'amount' => 'amount',
            'note' => 'note',
            'status' => 'status'
        ];
    }
}
