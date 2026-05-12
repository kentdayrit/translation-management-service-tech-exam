<?php

namespace App\Services;

use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;

class TranslationCacheService
{
    public function flushLocaleCache(string $locale): void
    {
        if (! $this->supportsTags()) {
            return;
        }

        Cache::tags(["translations_locale_{$locale}"])->flush();
    }

    public function supportsTags(): bool
    {
        return Cache::getStore() instanceof TaggableStore;
    }
}
