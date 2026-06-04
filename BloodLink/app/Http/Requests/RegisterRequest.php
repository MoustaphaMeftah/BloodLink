<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|regex:/^[0-9+\-\s()]*$/|min:10',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:donor,hospital,patient,admin',
            'city' => 'required|string|max:255',
            'blood_type' => 'required_if:role,donor|in:O+,O-,A+,A-,B+,B-,AB+,AB-',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'email.unique' => 'This email is already registered',
            'phone.regex' => 'Phone number format is invalid',
            'password.min' => 'Password must be at least 8 characters',
            'blood_type.required_if' => 'Blood type is required for donors',
        ];
    }
}
