<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IncomeStoreRequest extends FormRequest
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
            'income_category_id' => ['required', 'exists:income_categories,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
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
}