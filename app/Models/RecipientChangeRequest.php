<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipientChangeRequest extends Model
{
    protected $fillable = [
        'recipient_id',
        'hospital_id',
        'requested_by_user_id',
        'payload',
        'status',
        'hospital_note',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
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

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }
}
