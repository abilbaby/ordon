<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HospitalCreateInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'hospital';
    }

    public function rules(): array
    {
        return [
            // Required fields
            'recipient_name' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'digits_between:10,15'],
            'date_of_birth' => ['required', 'date', 'before:today', 'after:' . now()->subYears(150)->format('Y-m-d')],
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'blood_group' => ['required', Rule::in(['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'])],
            'organ_needed' => ['required', 'string', 'max:50'],
            'urgency_level' => ['required', Rule::in(['high', 'medium', 'low'])],
            
            // Optional fields
            'notes' => ['nullable', 'string', 'max:1000'],
            'medical_notes' => ['nullable', 'string', 'max:1000'],
            'contact_number' => ['nullable', 'digits_between:10,15'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'recipient_name' => $this->filled('recipient_name') ? trim((string) $this->input('recipient_name')) : null,
            'phone' => $this->filled('phone') ? preg_replace('/\D+/', '', (string) $this->input('phone')) : null,
            'contact_number' => $this->filled('contact_number') ? preg_replace('/\D+/', '', (string) $this->input('contact_number')) : null,
            'organ_needed' => $this->filled('organ_needed') ? trim((string) $this->input('organ_needed')) : null,
            'notes' => $this->filled('notes') ? trim((string) $this->input('notes')) : null,
            'medical_notes' => $this->filled('medical_notes') ? trim((string) $this->input('medical_notes')) : null,
        ]);
    }

    public function messages(): array
    {
        return [
            'recipient_name.required' => 'Recipient full name is required.',
            'recipient_name.min' => 'Recipient name must be at least 2 characters.',
            'recipient_name.max' => 'Recipient name cannot exceed 100 characters.',
            'recipient_name.regex' => 'Recipient name can only contain letters and spaces.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'Phone number is required.',
            'phone.digits_between' => 'Phone number must be between 10 and 15 digits.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Please select a valid gender.',
            'blood_group.required' => 'Blood group is required.',
            'blood_group.in' => 'Please select a valid blood group.',
            'organ_needed.required' => 'Organ needed is required.',
            'organ_needed.max' => 'Organ type cannot exceed 50 characters.',
            'urgency_level.required' => 'Urgency level is required.',
            'urgency_level.in' => 'Please select a valid urgency level.',
            'notes.max' => 'Medical notes cannot exceed 1000 characters.',
            'medical_notes.max' => 'Medical notes cannot exceed 1000 characters.',
            'contact_number.digits_between' => 'Contact number must be between 10 and 15 digits.',
        ];
    }

    public function attributes(): array
    {
        return [
            'recipient_name' => 'recipient full name',
            'date_of_birth' => 'date of birth',
            'organ_needed' => 'organ needed',
            'urgency_level' => 'urgency level',
            'contact_number' => 'contact number',
        ];
    }
}
