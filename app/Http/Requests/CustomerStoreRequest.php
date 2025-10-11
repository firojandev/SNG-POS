<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255','min:2'],
            'phone' => ['required','string','max:20','min:6'],
            'email' => ['nullable','email','max:255'],
            'address' => ['nullable','string','max:500'],
            'photo' => ['nullable','image','mimes:jpeg,png,jpg,gif','max:2048'],
            'is_active' => ['nullable','boolean'],
        ];
    }
}