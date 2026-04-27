<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class AllocationMatch extends Model
{
    use SoftDeletes;

    protected $table = 'matches';

    protected $fillable = [
        'donor_id',
        'recipient_id',
        'score',
        'match_score',
        'reason',
        'score_breakdown',
        'status',
        'admin_override',
        'override_reason',
    ];

    protected $appends = ['priority_level'];

    protected $casts = [
        'score_breakdown' => 'array',
        'admin_override' => 'boolean',
        'match_score' => 'integer',
    ];

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class);
    }

    public function transplant(): HasOne
    {
        return $this->hasOne(Transplant::class, 'match_id');
    }

    public function getPriorityLevelAttribute(): string
    {
        return match (true) {
            $this->score >= 90 => 'Critical',
            $this->score >= 65 => 'High',
            $this->score >= 40 => 'Medium',
            default => 'Standard',
        };
    }
}
