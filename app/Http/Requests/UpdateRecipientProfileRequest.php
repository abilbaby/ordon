<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRecipientProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'recipient';
    }

    public function rules(): array
    {
        return [
            // Personal info - editable
            'full_name' => ['nullable', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'phone' => ['nullable', 'digits_between:10,15'],
            'address' => ['nullable', 'string', 'min:5', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today', 'after:' . now()->subYears(150)->format('Y-m-d')],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'blood_group' => ['nullable', Rule::in(['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'])],
            'emergency_contact_name' => ['nullable', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'emergency_contact_phone' => ['nullable', 'digits_between:10,15'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'full_name' => $this->filled('full_name') ? trim((string) $this->input('full_name')) : null,
            'phone' => $this->filled('phone') ? preg_replace('/\D+/', '', (string) $this->input('phone')) : null,
            'address' => $this->filled('address') ? trim((string) $this->input('address')) : null,
            'emergency_contact_name' => $this->filled('emergency_contact_name') ? trim((string) $this->input('emergency_contact_name')) : null,
            'emergency_contact_phone' => $this->filled('emergency_contact_phone') ? preg_replace('/\D+/', '', (string) $this->input('emergency_contact_phone')) : null,
        ]);
    }

    public function messages(): array
    {
        return [
            'full_name.min' => 'Full name must be at least 2 characters.',
            'full_name.max' => 'Full name cannot exceed 100 characters.',
            'full_name.regex' => 'Full name can only contain letters and spaces.',
            'phone.digits_between' => 'Phone number must be between 10 and 15 digits.',
            'address.min' => 'Address must be at least 5 characters.',
            'address.max' => 'Address cannot exceed 255 characters.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'gender.in' => 'Please select a valid gender.',
            'blood_group.in' => 'Please select a valid blood group.',
            'emergency_contact_name.min' => 'Emergency contact name must be at least 2 characters.',
            'emergency_contact_name.max' => 'Emergency contact name cannot exceed 100 characters.',
            'emergency_contact_name.regex' => 'Emergency contact name can only contain letters and spaces.',
            'emergency_contact_phone.digits_between' => 'Emergency contact phone must be between 10 and 15 digits.',
        ];
    }

    public function attributes(): array
    {
        return [
            'full_name' => 'full name',
            'date_of_birth' => 'date of birth',
            'emergency_contact_name' => 'emergency contact name',
            'emergency_contact_phone' => 'emergency contact phone',
        ];
    }
}
