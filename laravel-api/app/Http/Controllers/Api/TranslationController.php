<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\TranslationException;
use App\Exceptions\TranslationExportException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchTranslationRequest;
use App\Http\Requests\StoreTranslationRequest;
use App\Http\Resources\TranslationCollection;
use App\Http\Resources\TranslationResource;
use App\Services\TranslationService;
use App\Traits\ApiResponser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class TranslationController extends Controller
{
    use ApiResponser;

    public function __construct(
        protected readonly TranslationService $translationService
    ) {}

    #[OA\Get(
        path: '/v1/translations/search',
        tags: ['Translations'],
        summary: 'Search translations by key, content, locale, or tag.',
        parameters: [
            new OA\Parameter(name: 'tag', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'key', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'content', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'locale', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Translations retrieved successfully.'),
            new OA\Response(response: 400, description: 'Invalid query parameters.'),
        ]
    )]
    public function index(SearchTranslationRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $results = $this->translationService->search($filters);

        return $this->successResponse(
            new TranslationCollection($results),
            'Translations retrieved successfully.'
        );
    }

    #[OA\Get(
        path: '/v1/translations/{id}',
        tags: ['Translations'],
        summary: 'Retrieve a single translation entry.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Translation retrieved successfully.'),
            new OA\Response(response: 404, description: 'Translation not found.'),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $translation = $this->translationService->find($id);

            return $this->successResponse(
                new TranslationResource($translation),
                'Translation retrieved successfully.'
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Translation not found.', 404);
        }
    }

    #[OA\Post(
        path: '/v1/translations',
        tags: ['Translations'],
        security: [['bearerAuth' => []]],
        summary: 'Create or update a translation entry.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'key', type: 'string', maxLength: 255),
                    new OA\Property(property: 'locale', type: 'string'),
                    new OA\Property(property: 'content', type: 'string'),
                    new OA\Property(property: 'tags', type: 'array', items: new OA\Items(type: 'string')),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Translation saved successfully.'),
            new OA\Response(response: 400, description: 'Validation failed.'),
            new OA\Response(response: 401, description: 'Authentication required.'),
            new OA\Response(response: 500, description: 'Server error while saving translation.'),
        ]
    )]
    public function store(StoreTranslationRequest $request): JsonResponse
    {
        try {
            $requestData = $request->validated();
            $translation = $this->translationService->upsert($requestData);

            return $this->successResponse(
                new TranslationResource($translation),
                'Translation saved successfully.',
                201
            );
        } catch (TranslationException $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    #[OA\Get(
        path: '/v1/translations/export/{locale}',
        tags: ['Translations'],
        summary: 'Export all translations for a locale.',
        parameters: [
            new OA\Parameter(name: 'locale', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Translations exported successfully.'),
            new OA\Response(response: 404, description: 'No translations found for the locale.'),
            new OA\Response(response: 500, description: 'Server error.'),
        ]
    )]
    public function export(string $locale): JsonResponse
    {
        try {
            $data = $this->translationService->getExportData($locale);

            return $this->successResponse($data, 'Translations exported successfully.');
        } catch (TranslationExportException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        } catch (TranslationException $e) {
            return $this->errorResponse($e->getMessage(), 500);
        } catch (\Throwable $e) {
            return $this->errorResponse('Export failed: ' . $e->getMessage(), 500);
        }
    }
}
