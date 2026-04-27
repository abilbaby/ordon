<?php

namespace App\Enums;

enum OrganType: string
{
    case Kidney = 'Kidney';
    case Liver = 'Liver';
    case Heart = 'Heart';
    case Lung = 'Lung';
    case Pancreas = 'Pancreas';
    case Intestine = 'Intestine';
    case Cornea = 'Cornea';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function livingAllowedValues(): array
    {
        return [
            self::Kidney->value,
            self::Liver->value,
            self::Lung->value,
        ];
    }
}
