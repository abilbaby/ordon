<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyRequest extends Model
{
    protected $fillable = [
        'recipient_id',
        'organ_type',
        'blood_group',
        'status',
        'accepted_donor_id',
    ];

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class);
    }

    public function acceptedDonor(): BelongsTo
    {
        return $this->belongsTo(Donor::class, 'accepted_donor_id');
    }
}
