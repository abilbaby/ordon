<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipientUpdateRequest extends Model
{

    protected $fillable = [
        'recipient_id',
        'hospital_id',
        'requested_by_user_id',
        'reviewed_by',
        'requested_full_name',
        'requested_dob',
        'requested_gender',
        'requested_blood_group',
        'requested_organ_needed',
        'requested_urgency_level',
        'requested_waiting_time',
        'requested_other_organs',
        'reason',
        'status',
        'reviewer_note',
    ];

    protected function casts(): array
    {
        return [
            'requested_dob' => 'date',
            'requested_waiting_time' => 'integer',
            'requested_other_organs' => 'array',
        ];
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class);
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if recipient has a pending request
     */
    public static function hasPendingRequest(int $recipientId): bool
    {
        return static::where('recipient_id', $recipientId)
            ->where('status', 'Pending')
            ->exists();
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope for hospital requests
     */
    public function scopeForHospital($query, int $hospitalId)
    {
        return $query->where('hospital_id', $hospitalId);
    }
}
