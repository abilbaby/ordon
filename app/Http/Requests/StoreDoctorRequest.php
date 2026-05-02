<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'hospital';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'specialization' => ['required', 'string', 'min:2', 'max:100'],
            'phone' => ['nullable', 'digits_between:10,15'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->filled('name') ? trim((string) $this->input('name')) : null,
            'specialization' => $this->filled('specialization') ? trim((string) $this->input('specialization')) : null,
            'phone' => $this->filled('phone') ? preg_replace('/\D+/', '', (string) $this->input('phone')) : null,
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Doctor name is required.',
            'name.min' => 'Doctor name must be at least 2 characters.',
            'name.max' => 'Doctor name cannot exceed 100 characters.',
            'name.regex' => 'Doctor name can only contain letters and spaces.',
            'specialization.required' => 'Specialization is required.',
            'specialization.min' => 'Specialization must be at least 2 characters.',
            'specialization.max' => 'Specialization cannot exceed 100 characters.',
            'phone.digits_between' => 'Phone number must be between 10 and 15 digits.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'doctor name',
            'specialization' => 'specialization',
        ];
    }
}
