<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipientVerification extends Model
{
    protected $fillable = [
        'rvid',
        'hospital_id',
        'recipient_name',
        'email',
        'phone',
        'blood_group',
        'notes',
        'registration_link',
        'status',
        'expires_at',
        'date_of_birth',
        'gender',
        'organ_needed',
        'urgency_level',
        'medical_notes',
        'contact_number',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }
}
