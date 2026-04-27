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
