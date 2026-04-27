<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogger
{
    public function log(?User $user, string $module, string $action, ?string $details = null): void
    {
        AuditLog::create([
            'user_id' => $user?->id,
            'module' => $module,
            'action' => $action,
            'details' => $details,
        ]);
    }
}
