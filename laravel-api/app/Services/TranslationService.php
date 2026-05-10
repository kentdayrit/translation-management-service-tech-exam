<?php

namespace App\Services;

use App\Exceptions\TranslationException;
use App\Exceptions\TranslationExportException;
use App\Models\Translation;
use App\Services\TranslationCacheService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class TranslationService
{
    public function __construct(
        protected readonly TranslationCacheService $cacheService,
    ) {}

    public function search(array $filters): LengthAwarePaginator
    {
        $cacheKey = 'translations_search_' . md5(serialize($filters));
        $ttl = 300; // 5 minutes

        return Cache::remember($cacheKey, $ttl, function () use ($filters) {
            $query = Translation::filter($filters);
            $perPage = $filters['per_page'] ?? 50;

            return $query->paginate($perPage);
        });
    }

    public function getExportData(string $locale): array
    {
        $cacheKey = "translations_export_{$locale}";
        $ttl = 600; // 10 minutes

        return Cache::remember($cacheKey, $ttl, function () use ($locale) {
            $translations = Translation::where('locale', $locale)
                ->pluck('content', 'key')
                ->toArray();

            if (empty($translations)) {
                throw new TranslationExportException("No translations available for locale: {$locale}");
            }

            return $translations;
        });
    }

    public function upsert(array $data): Translation
    {
        try {
            $translation = Translation::updateOrCreate(
                ['key' => $data['key'], 'locale' => $data['locale']],
                ['content' => $data['content'], 'tags' => $data['tags'] ?? []]
            );
        } catch (\Throwable $e) {
            throw new TranslationException('Unable to save the translation.', 0, $e);
        }

        $this->cacheService->flushLocaleCache($data['locale']);
        Cache::forget("translations_export_{$data['locale']}");

        return $translation;
    }
}
