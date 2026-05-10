<?php

namespace Tests\Feature;

use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_performance_with_large_dataset()
    {
        // Seed 1k records for performance testing
        Translation::factory()->count(1000)->create();

        // Warm up cache
        $this->getJson('/api/v1/translations/search?locale=en&per_page=100');

        $startTime = microtime(true);

        $response = $this->getJson('/api/v1/translations/search?locale=en&per_page=100');

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Assert response is successful
        $response->assertStatus(200);

        // Assert execution time is under 200 milliseconds
        $this->assertLessThan(0.2, $executionTime, "Search took too long: {$executionTime} seconds");

        // Log execution time for monitoring
        $this->addToAssertionCount(1); // Prevent "no assertions" warning
        echo "Search execution time: {$executionTime} seconds\n";
    }

    public function test_export_performance()
    {
        Translation::factory()->count(500)->create(['locale' => 'en']);

        // Warm up cache
        $this->getJson('/api/v1/translations/export/en');

        $startTime = microtime(true);

        $response = $this->getJson('/api/v1/translations/export/en');

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $response->assertStatus(200);

        $this->assertLessThan(0.5, $executionTime, "Export took too long: {$executionTime} seconds");

        echo "Export execution time: {$executionTime} seconds\n";
    }
}