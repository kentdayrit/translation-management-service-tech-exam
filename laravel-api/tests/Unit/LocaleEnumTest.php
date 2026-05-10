<?php

namespace Tests\Unit;

use App\Enums\Locale;
use App\Enums\Tag;
use Tests\TestCase;

class LocaleEnumTest extends TestCase
{
    public function test_locale_enum_values()
    {
        $expected = ['en', 'fr', 'es'];

        $this->assertEquals($expected, Locale::values());
    }

    public function test_locale_enum_cases()
    {
        $this->assertEquals('en', Locale::EN->value);
        $this->assertEquals('fr', Locale::FR->value);
        $this->assertEquals('es', Locale::ES->value);
    }
}