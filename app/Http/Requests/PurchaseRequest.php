<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.tax_id' => 'nullable|exists:taxes,id',
            'items.*.tax_amount' => 'nullable|numeric|min:0',
            'items.*.unit_total' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'due_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Prepare the data for validation.
     */
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
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Please select a supplier.',
            'supplier_id.exists' => 'The selected supplier is invalid.',
            'date.required' => 'Purchase date is required.',
            'date.date' => 'Purchase date must be a valid date.',
            'items.required' => 'Please add at least one item to the purchase.',
            'items.min' => 'Please add at least one item to the purchase.',
            'items.*.product_id.required' => 'Product is required for each item.',
            'items.*.product_id.exists' => 'The selected product is invalid.',
            'items.*.unit_price.required' => 'Unit price is required for each item.',
            'items.*.unit_price.numeric' => 'Unit price must be a valid number.',
            'items.*.unit_price.min' => 'Unit price must be greater than or equal to 0.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.integer' => 'Quantity must be a whole number.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.numeric' => 'Total amount must be a valid number.',
            'total_amount.min' => 'Total amount must be greater than or equal to 0.',
            'paid_amount.required' => 'Paid amount is required.',
            'paid_amount.numeric' => 'Paid amount must be a valid number.',
            'paid_amount.min' => 'Paid amount must be greater than or equal to 0.',
            'due_amount.required' => 'Due amount is required.',
            'due_amount.numeric' => 'Due amount must be a valid number.',
            'due_amount.min' => 'Due amount must be greater than or equal to 0.',
            'note.max' => 'Note cannot exceed 1000 characters.',
        ];
    }
}
