<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDonorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->id() === (int)$this->route('id') || auth()->user()->role === 'admin');
    }

    public function rules(): array
    {
        return [
            'blood_type' => 'sometimes|in:O+,O-,A+,A-,B+,B-,AB+,AB-',
            'city' => 'sometimes|string|max:255',
            'availability' => 'sometimes|boolean',
            'medical_history' => 'sometimes|nullable|string|max:1000',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.between' => 'Latitude must be between -90 and 90',
            'longitude.between' => 'Longitude must be between -180 and 180',
        ];
    }
}
