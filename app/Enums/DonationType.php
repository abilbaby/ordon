<?php

namespace App\Enums;

enum DonationType: string
{
    case LivingDonation = 'LivingDonation';
    case AfterDeathDonation = 'AfterDeathDonation';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
