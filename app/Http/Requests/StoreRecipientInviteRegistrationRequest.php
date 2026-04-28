<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRecipientInviteRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rvid' => ['required', 'string'],
            'identity_type' => [
                'required',
                Rule::in(['aadhaar', 'passport', 'voter_id', 'driving_licence', 'pan', 'other']),
            ],
            'identity_number' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $number = (string) $value;

                    match ($this->input('identity_type')) {
                        'aadhaar' => preg_match('/^\d{12}$/', $number) === 1
                            ?: $fail('Aadhaar number must be exactly 12 digits.'),
                        'pan' => preg_match('/^[A-Z]{5}\d{4}[A-Z]$/', $number) === 1
                            ?: $fail('PAN must follow the format ABCDE1234F.'),
                        'passport' => preg_match('/^[A-Z0-9]{6,9}$/', $number) === 1
                            ?: $fail('Passport number must be 6 to 9 alphanumeric characters.'),
                        'driving_licence' => preg_match('/^[A-Z0-9]{10,16}$/', $number) === 1
                            ?: $fail('Driving licence number must be 10 to 16 alphanumeric characters.'),
                        'voter_id' => preg_match('/^[A-Z0-9]{10}$/', $number) === 1
                            ?: $fail('Voter ID must be exactly 10 alphanumeric characters.'),
                        'other' => preg_match('/^[A-Z0-9]{5,20}$/', $number) === 1
                            ?: $fail('ID number must be 5 to 20 alphanumeric characters.'),
                        default => $fail('Select a valid government ID type first.'),
                    };
                },
            ],
            'password' => ['required', 'confirmed', 'min:8'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'identity_type' => $this->filled('identity_type') ? strtolower((string) $this->input('identity_type')) : null,
            'identity_number' => $this->filled('identity_number')
                ? strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', trim((string) $this->input('identity_number'))))
                : null,
        ]);
    }

    public function attributes(): array
    {
        return [
            'identity_type' => 'government ID type',
            'identity_number' => 'government ID number',
        ];
    }
}
