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
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected function casts(): array
    {
        return [
            'organs_needed' => 'array',
            'date_of_birth' => 'date',
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

    public function updateRequests(): HasMany
    {
        return $this->hasMany(RecipientUpdateRequest::class);
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

    /**
     * Get fields that are directly editable (no approval required)
     */
    public static function getDirectlyEditableFields(): array
    {
        return [
            'phone' => 'Phone Number',
            'address' => 'Address',
            'emergency_contact_name' => 'Emergency Contact Name',
            'emergency_contact_phone' => 'Emergency Contact Phone',
        ];
    }

    /**
     * Get fields that require approval (identity + medical critical)
     */
    public static function getApprovalRequiredFields(): array
    {
        return [
            'full_name' => 'Full Name',
            'date_of_birth' => 'Date of Birth',
            'gender' => 'Gender',
            'blood_group' => 'Blood Group',
            'organ_needed' => 'Organ Needed',
            'urgency_level' => 'Urgency Level',
            'waiting_time' => 'Waiting Time (days)',
            'organs_needed' => 'Other Organs Needed',
        ];
    }

    /**
     * Get current values for approval-required fields
     */
    public function getApprovalRequiredValues(): array
    {
        return [
            'full_name' => $this->user?->name,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender' => $this->gender,
            'blood_group' => $this->blood_group,
            'organ_needed' => $this->organ_needed,
            'urgency_level' => $this->urgency_level,
            'waiting_time' => $this->waiting_time,
            'organs_needed' => $this->organs_needed,
        ];
    }

    /**
     * Check if field requires approval
     */
    public static function requiresApproval(string $field): bool
    {
        return array_key_exists($field, self::getApprovalRequiredFields());
    }

    /**
     * Check if field is directly editable
     */
    public static function isDirectlyEditable(string $field): bool
    {
        return array_key_exists($field, self::getDirectlyEditableFields());
    }
}
