<?php

namespace Tests\Unit;

use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_scope_filter_by_locale()
    {
        Translation::factory()->create(['locale' => 'en', 'key' => 'test1']);
        Translation::factory()->create(['locale' => 'fr', 'key' => 'test2']);

        $results = Translation::filter(['locale' => 'en'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('en', $results->first()->locale);
    }

    public function test_scope_filter_by_key()
    {
        Translation::factory()->create(['key' => 'app.test']);
        Translation::factory()->create(['key' => 'other.test']);

        $results = Translation::filter(['key' => 'app'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('app.test', $results->first()->key);
    }

    public function test_scope_filter_by_tag()
    {
        Translation::factory()->create(['tags' => ['web']]);
        Translation::factory()->create(['tags' => ['mobile']]);

        $results = Translation::filter(['tag' => 'web'])->get();

        $this->assertCount(1, $results);
        $this->assertContains('web', $results->first()->tags);
    }

    public function test_scope_filter_by_content()
    {
        Translation::factory()->create(['content' => 'Hello world']);
        Translation::factory()->create(['content' => 'Goodbye world']);

        $results = Translation::filter(['content' => 'Hello'])->get();

        $this->assertCount(1, $results);
        $this->assertStringContainsString('Hello', $results->first()->content);
    }

    public function test_scope_filter_combined()
    {
        Translation::factory()->create(['locale' => 'en', 'key' => 'app.test', 'tags' => ['web']]);
        Translation::factory()->create(['locale' => 'fr', 'key' => 'app.test', 'tags' => ['web']]);

        $results = Translation::filter(['locale' => 'en', 'tag' => 'web'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('en', $results->first()->locale);
    }
}