<?php

namespace App\Http\Requests;

use App\Models\Supplier;
use App\Models\PaymentToSupplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PaymentToSupplierUpdateRequest extends FormRequest
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
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->supplier_id && $this->amount) {
                $supplier = Supplier::find($this->supplier_id);
                $payment = $this->route('paymentToSupplier');

                if ($supplier && $payment) {
                    // Calculate available balance
                    $availableBalance = $supplier->balance;

                    // If same supplier, add back the old payment amount
                    if ($payment->supplier_id == $this->supplier_id) {
                        $availableBalance += $payment->amount;
                    }

                    if ($this->amount > $availableBalance) {
                        $validator->errors()->add('amount', 'Payment amount cannot exceed available balance of ' . get_option('app_currency', '$') . number_format($availableBalance, 2));
                    }
                }
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $date = $this->input('payment_date');
        if ($date) {
            $phpFormat = get_option('date_format', 'Y-m-d');
            try {
                $normalized = \Carbon\Carbon::createFromFormat($phpFormat, $date)->format('Y-m-d');
                $this->merge(['payment_date' => $normalized]);
            } catch (\Exception $e) {
                // leave as is; validation will catch invalid date
            }
        }
    }
}
