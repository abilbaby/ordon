<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignTransplantSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'hospital';
    }

    public function rules(): array
    {
        $hospitalId = auth()->user()?->hospital?->id;

        return [
            'slot_date' => ['required', 'date'],
            'slot_period' => ['required', Rule::in(['Morning', 'Afternoon', 'Evening'])],
            'operating_room' => ['required', 'string', 'max:100'],
            'doctor_id' => [
                'required',
                Rule::exists('doctors', 'id')->where(fn ($query) => $query->where('hospital_id', $hospitalId)),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'operating_room' => $this->filled('operating_room') ? trim((string) $this->input('operating_room')) : null,
        ]);
    }
}
