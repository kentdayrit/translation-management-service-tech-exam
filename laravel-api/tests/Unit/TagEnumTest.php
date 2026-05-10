<?php

namespace Tests\Unit;

use App\Enums\Tag;
use Tests\TestCase;

class TagEnumTest extends TestCase
{
    public function test_tag_enum_values()
    {
        $expected = ['web', 'mobile', 'desktop'];

        $this->assertEquals($expected, Tag::values());
    }

    public function test_tag_enum_cases()
    {
        $this->assertEquals('web', Tag::WEB->value);
        $this->assertEquals('mobile', Tag::MOBILE->value);
        $this->assertEquals('desktop', Tag::DESKTOP->value);
    }
}