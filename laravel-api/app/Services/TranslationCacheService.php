<?php

namespace App\Services;

use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;

class TranslationCacheService
{
    public function flushLocaleCache(string $locale): void
    {
        $store = Cache::getStore();

        if ($store instanceof TaggableStore) {
            Cache::tags(["locale_{$locale}"])->flush();
        }
    }
}
