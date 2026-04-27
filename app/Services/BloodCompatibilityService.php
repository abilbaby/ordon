<?php

namespace App\Services;

class BloodCompatibilityService
{
    /**
     * @var array<string, array<int, string>>
     */
    private const COMPATIBILITY = [
        'O-' => ['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'],
        'O+' => ['O+', 'A+', 'B+', 'AB+'],
        'A-' => ['A-', 'A+', 'AB-', 'AB+'],
        'A+' => ['A+', 'AB+'],
        'B-' => ['B-', 'B+', 'AB-', 'AB+'],
        'B+' => ['B+', 'AB+'],
        'AB-' => ['AB-', 'AB+'],
        'AB+' => ['AB+'],
    ];

    public function canDonateTo(string $donorBloodGroup, string $recipientBloodGroup): bool
    {
        return in_array($recipientBloodGroup, self::COMPATIBILITY[$donorBloodGroup] ?? [], true);
    }
}
