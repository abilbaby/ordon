<?php

namespace App\Observers;

use App\Models\StatusHistory;
use Illuminate\Database\Eloquent\Model;

class StatusHistoryObserver
{
    public function updated(Model $model): void
    {
        $statusColumn = $this->statusColumn($model);
        if (! $statusColumn || ! $model->wasChanged($statusColumn)) {
            return;
        }

        StatusHistory::create([
            'entity_id' => $model->getKey(),
            'entity_type' => $model::class,
            'old_status' => (string) $model->getOriginal($statusColumn),
            'new_status' => (string) $model->getAttribute($statusColumn),
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);
    }

    private function statusColumn(Model $model): ?string
    {
        return match ($model::class) {
            \App\Models\Donor::class => 'medical_status',
            \App\Models\Recipient::class => 'status',
            \App\Models\AllocationMatch::class => 'status',
            \App\Models\Transplant::class => 'status',
            default => null,
        };
    }
}
