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
        $query = Translation::filter($filters)
            ->orderBy('key')
            ->orderBy('locale');

        $perPage = $filters['per_page'] ?? 50;

        return $query->paginate($perPage);
    }

    public function getExportData(string $locale): array
    {
        $cacheKey = "translations_export_{$locale}";
        $ttl = 600; // 10 minutes
        $cacheTags = ["translations_locale_{$locale}"];

        if ($this->cacheService->supportsTags()) {
            return Cache::tags($cacheTags)->remember($cacheKey, $ttl, function () use ($locale) {
                return $this->loadExportData($locale);
            });
        }

        return Cache::remember($cacheKey, $ttl, function () use ($locale) {
            return $this->loadExportData($locale);
        });
    }

    protected function loadExportData(string $locale): array
    {
        $translations = Translation::where('locale', $locale)
            ->pluck('content', 'key')
            ->toArray();

        if (empty($translations)) {
            throw new TranslationExportException("No translations available for locale: {$locale}");
        }

        return $translations;
    }

    public function find(int $id): Translation
    {
        return Translation::findOrFail($id);
    }

    public function upsert(array $data): Translation
    {
        try {
            $translation = Translation::firstOrNew(
                ['key' => $data['key'], 'locale' => $data['locale']]
            );

            $translation->content = $data['content'];

            if (array_key_exists('tags', $data)) {
                $translation->tags = $data['tags'] ?? [];
            } elseif (! $translation->exists) {
                $translation->tags = [];
            }

            $translation->save();
        } catch (\Throwable $e) {
            throw new TranslationException('Unable to save the translation.', 0, $e);
        }

        $this->cacheService->flushLocaleCache($data['locale']);
        Cache::forget("translations_export_{$data['locale']}");

        return $translation;
    }
}
