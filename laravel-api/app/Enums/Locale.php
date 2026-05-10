<?php

namespace App\Enums;

enum Locale: string
{
    case EN = 'en';
    case FR = 'fr';
    case ES = 'es';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
