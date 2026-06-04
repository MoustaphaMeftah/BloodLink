<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BloodRequestStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'hospital';
    }

    public function rules(): array
    {
        return [
            'hospital_id' => 'required|exists:hospitals,id',
            'blood_type' => 'required|in:O+,O-,A+,A-,B+,B-,AB+,AB-',
            'quantity' => 'required|integer|min:100|max:10000',
            'urgency' => 'required|in:low,medium,high,critical',
            'location' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'hospital_id.required' => 'Hospital ID is required',
            'hospital_id.exists' => 'The selected hospital does not exist',
            'blood_type.in' => 'Invalid blood type',
            'quantity.min' => 'Minimum quantity is 100ml',
            'quantity.max' => 'Maximum quantity is 10000ml',
            'urgency.in' => 'Urgency must be: low, medium, high, or critical',
        ];
    }
}
