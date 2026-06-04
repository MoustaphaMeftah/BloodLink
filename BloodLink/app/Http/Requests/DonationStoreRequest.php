<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'donor_id' => 'required|exists:donors,id',
            'blood_request_id' => 'nullable|exists:blood_requests,id',
            'donation_date' => 'required|date|before_or_equal:today',
            'quantity' => 'required|integer|min:100|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'donor_id.required' => 'Donor ID is required',
            'donor_id.exists' => 'The selected donor does not exist',
            'donation_date.before_or_equal' => 'Donation date cannot be in the future',
            'quantity.min' => 'Minimum donation quantity is 100ml',
            'quantity.max' => 'Maximum donation quantity is 500ml',
        ];
    }
}
