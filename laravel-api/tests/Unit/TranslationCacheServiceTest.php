<?php

namespace Tests\Unit;

use App\Services\TranslationCacheService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TranslationCacheServiceTest extends TestCase
{
    public function test_flush_locale_cache_with_supported_store()
    {
        // Assuming Redis or similar is configured
        Cache::shouldReceive('getStore')
            ->andReturn($this->mock(\Illuminate\Cache\TaggableStore::class));

        Cache::shouldReceive('tags->flush')
            ->once();

        $service = new TranslationCacheService();
        $service->flushLocaleCache('en');
    }

    public function test_flush_locale_cache_with_unsupported_store()
    {
        // Mock unsupported store
        $store = $this->mock(\Illuminate\Cache\Repository::class);
        Cache::shouldReceive('getStore')
            ->andReturn($store);

        // Should not call tags
        Cache::shouldNotReceive('tags');

        $service = new TranslationCacheService();
        $service->flushLocaleCache('en');
    }
}