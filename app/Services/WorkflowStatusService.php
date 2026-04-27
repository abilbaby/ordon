<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class WorkflowStatusService
{
    private const FLOW = ['REGISTERED', 'VERIFIED', 'MATCHED', 'APPROVED', 'COMPLETED'];

    public function advanceStatus(Model $model, string $column, string $nextStatus): void
    {
        $currentStatus = (string) $model->{$column};
        $currentIndex = array_search($currentStatus, self::FLOW, true);
        $nextIndex = array_search($nextStatus, self::FLOW, true);

        if ($currentIndex === false || $nextIndex === false || $nextIndex !== $currentIndex + 1) {
            throw new InvalidArgumentException('Invalid status transition.');
        }

        $model->update([$column => $nextStatus]);
    }

    public function nextStatus(string $currentStatus): ?string
    {
        $currentIndex = array_search($currentStatus, self::FLOW, true);
        if ($currentIndex === false || ! isset(self::FLOW[$currentIndex + 1])) {
            return null;
        }

        return self::FLOW[$currentIndex + 1];
    }
}
