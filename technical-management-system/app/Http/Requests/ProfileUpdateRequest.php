<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_business_name' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'string', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'customer_address' => ['nullable', 'string'],
            'customer_city' => ['nullable', 'string', 'max:100'],
            'customer_state' => ['nullable', 'string', 'max:100'],
            'customer_postal_code' => ['nullable', 'string', 'max:20'],
            'customer_country' => ['nullable', 'string', 'max:100'],
            'customer_contact_person' => ['nullable', 'string', 'max:255'],
            'customer_industry_type' => ['nullable', 'string', 'max:255'],
            'customer_tax_id' => ['nullable', 'string', 'max:100'],
            'customer_credit_terms' => ['nullable', 'integer', 'min:0'],
            'customer_notes' => ['nullable', 'string'],
        ];
    }
}
