<?php

namespace App\Enums;

enum Tag: string
{
    case WEB = 'web';
    case MOBILE = 'mobile';
    case DESKTOP = 'desktop';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
