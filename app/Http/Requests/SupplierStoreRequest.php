<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255','min:2'],
            'contact_person' => ['nullable','string','max:255'],
            'phone' => ['required','string','max:20','min:6'],
            'email' => ['nullable','email','max:255'],
            'address' => ['nullable','string','max:500'],
            'about' => ['nullable','string','max:2000'],
            'balance' => ['nullable','numeric','min:0'],
            'photo' => ['nullable','image','mimes:jpeg,png,jpg,gif','max:2048'],
            'is_active' => ['nullable','boolean'],
        ];
    }
}


