<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'approved',
        'fraud_flag',
        'blacklisted',
        'blood_group',
        'organ_type',
        'region',
        'medical_conditions',
        'donation_type',
        'notes',
        'donation_preferences',
        'consent_given',
        'family_contact',
        'identity_type',
        'identity_number',
        'identity_verified',
        'pre_donation_checklist_completed',
        'eligibility_status',
        'medical_status',
        'is_available',
        'available_until',
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
            'available_until' => 'datetime',
            'approved' => 'boolean',
            'fraud_flag' => 'boolean',
            'blacklisted' => 'boolean',
            'donation_preferences' => 'array',
            'consent_given' => 'boolean',
            'identity_verified' => 'boolean',
            'pre_donation_checklist_completed' => 'boolean',
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

    public function organs(): HasMany
    {
        return $this->hasMany(DonorOrgan::class);
    }

    public function donationHistory(): HasMany
    {
        return $this->hasMany(DonationHistory::class);
    }
}
