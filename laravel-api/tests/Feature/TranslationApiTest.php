<?php

namespace Tests\Feature;

use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationApiTest extends TestCase
{
    use RefreshDatabase;
    
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_search_translations()
    {
        Translation::factory()->create(['locale' => 'en', 'key' => 'app.test']);
        Translation::factory()->create(['locale' => 'fr', 'key' => 'app.test']);

        $response = $this->getJson('/api/v1/translations/search?locale=en');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'key', 'locale', 'content', 'tags']
                ],
                'links',
                'meta' => ['total', 'per_page', 'current_page'],
                'status',
                'message'
            ]);
    }

    public function test_search_with_invalid_locale()
    {
        $response = $this->getJson('/api/v1/translations/search?locale=invalid');

        $response->assertStatus(422)
            ->assertJsonValidationErrors('locale');
    }

    public function test_store_translation_requires_auth()
    {
        $data = [
            'key' => 'app.test',
            'locale' => 'en',
            'content' => 'Test content',
            'tags' => ['web']
        ];

        $response = $this->postJson('/api/v1/translations', $data);

        $response->assertStatus(401);
    }

    public function test_store_translation_authenticated()
    {
        $this->actingAs($this->user);

        $data = [
            'key' => 'app.test',
            'locale' => 'en',
            'content' => 'Test content',
            'tags' => ['web']
        ];

        $response = $this->postJson('/api/v1/translations', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'key', 'locale', 'content', 'tags']
            ]);

        $this->assertDatabaseHas('translations', [
            'key' => 'app.test',
            'locale' => 'en',
            'content' => 'Test content',
            'tags' => json_encode(['web'])
        ]);
    }

    public function test_store_translation_validation_error()
    {
        $this->actingAs($this->user);

        $data = ['locale' => 'en']; // Missing required fields

        $response = $this->postJson('/api/v1/translations', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['key', 'content']);
    }

    public function test_export_translations()
    {
        Translation::factory()->create(['locale' => 'en', 'key' => 'test', 'content' => 'Hello']);

        $response = $this->getJson('/api/v1/translations/export/en');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'Success',
                'message' => 'Translations exported successfully.',
                'data' => ['test' => 'Hello']
            ]);
    }

    public function test_export_nonexistent_locale()
    {
        $response = $this->getJson('/api/v1/translations/export/en');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'Error',
                'message' => 'No translations available for locale: en'
            ]);
    }
}