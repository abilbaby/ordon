<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'urgency_weight',
        'waiting_weight',
        'compatibility_weight',
        'emergency_threshold',
        'max_daily_surgeries',
    ];
}
