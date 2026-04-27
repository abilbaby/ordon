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
            'identity_number' => ['required', 'string', 'min:4', 'max:60'],
            'password' => ['required', 'confirmed', 'min:8'],
        ];
    }
}
