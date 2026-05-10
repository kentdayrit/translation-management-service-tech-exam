<?php

namespace Tests\Unit;

use App\Exceptions\TranslationException;
use App\Exceptions\TranslationExportException;
use App\Models\Translation;
use App\Services\TranslationCacheService;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TranslationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TranslationService(
            app(TranslationCacheService::class)
        );
    }

    public function test_search_with_filters()
    {
        Translation::factory()->create(['locale' => 'en', 'key' => 'app.test']);
        Translation::factory()->create(['locale' => 'fr', 'key' => 'app.test']);

        $results = $this->service->search(['locale' => 'en']);

        $this->assertCount(1, $results->items());
        $this->assertEquals('en', $results->items()[0]->locale);
    }

    public function test_search_with_pagination()
    {
        Translation::factory()->count(5)->create();

        $results = $this->service->search(['per_page' => 3]);

        $this->assertCount(3, $results->items());
        $this->assertEquals(5, $results->total());
    }

    public function test_get_export_data_success()
    {
        Translation::factory()->create(['locale' => 'en', 'key' => 'test', 'content' => 'Hello']);

        $data = $this->service->getExportData('en');

        $this->assertEquals(['test' => 'Hello'], $data);
    }

    public function test_get_export_data_empty_throws_exception()
    {
        $this->expectException(TranslationExportException::class);

        $this->service->getExportData('en');
    }

    public function test_upsert_creates_new_translation()
    {
        $data = [
            'key' => 'app.test',
            'locale' => 'en',
            'content' => 'Test content',
            'tags' => ['web']
        ];

        $translation = $this->service->upsert($data);

        $this->assertDatabaseHas('translations', [
            'key' => 'app.test',
            'locale' => 'en',
            'content' => 'Test content',
            'tags' => json_encode(['web'])
        ]);
        $this->assertEquals('app.test', $translation->key);
    }

    public function test_upsert_updates_existing_translation()
    {
        $existing = Translation::factory()->create(['key' => 'app.test', 'locale' => 'en', 'content' => 'Old']);

        $data = [
            'key' => 'app.test',
            'locale' => 'en',
            'content' => 'New content',
            'tags' => ['web']
        ];

        $translation = $this->service->upsert($data);

        $this->assertEquals($existing->id, $translation->id);
        $this->assertEquals('New content', $translation->content);
        $this->assertDatabaseHas('translations', [
            'key' => 'app.test',
            'locale' => 'en',
            'content' => 'New content',
            'tags' => json_encode(['web'])
        ]);
    }

    public function test_upsert_throws_exception_on_failure()
    {
        // Mock a failure scenario, e.g., database error
        $this->expectException(TranslationException::class);

        // Force an error by passing invalid data or mocking
        $data = ['key' => null, 'locale' => 'en', 'content' => 'test'];

        $this->service->upsert($data);
    }
}