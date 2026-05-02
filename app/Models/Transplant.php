<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transplant extends Model
{
    protected $fillable = [
        'match_id',
        'hospital_id',
        'doctor_id',
        'status',
        'scheduled_at',
        'slot_date',
        'slot_period',
        'operating_room',
        'surgeon_name',
        'surgery_status',
        'transport_status',
        'post_operation_report',
        'recipient_name_override',
        'certificate_id',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'slot_date' => 'date',
        ];
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(AllocationMatch::class, 'match_id');
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
