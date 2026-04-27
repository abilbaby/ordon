<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'hospital_id',
        'recipient_verification_id',
        'blood_group',
        'organ_needed',
        'region',
        'organs_needed',
        'medical_record_path',
        'identity_type',
        'identity_number',
        'identity_number_hash',
        'identity_number_last4',
        'identity_verified',
        'doctor_approved',
        'hospital_verified',
        'admin_approved',
        'flagged_for_review',
        'priority_escalation_requested',
        'is_emergency',
        'urgency_level',
        'waiting_time',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'organs_needed' => 'array',
            'identity_verified' => 'boolean',
            'doctor_approved' => 'boolean',
            'hospital_verified' => 'boolean',
            'admin_approved' => 'boolean',
            'flagged_for_review' => 'boolean',
            'priority_escalation_requested' => 'boolean',
            'is_emergency' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(AllocationMatch::class);
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(RecipientChangeRequest::class);
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function recipientVerification(): BelongsTo
    {
        return $this->belongsTo(RecipientVerification::class);
    }

    public function getMaskedIdentityAttribute(): string
    {
        if ($this->identity_number_last4) {
            return '****'.$this->identity_number_last4;
        }

        if (! $this->identity_number) {
            return 'N/A';
        }

        return str_repeat('*', max(strlen($this->identity_number) - 4, 0)).substr($this->identity_number, -4);
    }
}
