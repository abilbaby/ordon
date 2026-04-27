<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hospital extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'location',
        'approved',
        'fraud_flag',
        'blacklisted',
        'identity_type',
        'identity_number',
        'identity_verified',
    ];

    protected function casts(): array
    {
        return [
            'approved' => 'boolean',
            'fraud_flag' => 'boolean',
            'blacklisted' => 'boolean',
            'identity_verified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transplants(): HasMany
    {
        return $this->hasMany(Transplant::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(OrganInventory::class);
    }

    public function recipientInvites(): HasMany
    {
        return $this->hasMany(RecipientVerification::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(Recipient::class);
    }
}
