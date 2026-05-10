<?php

namespace Database\Factories;

use App\Enums\Locale;
use App\Enums\Tag;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Translation>
 */
class TranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => sprintf('%s.%s', $this->faker->unique()->numerify('translation-#####'), $this->faker->word()),
            'locale' => $this->faker->randomElement(Locale::values()),
            'content' => $this->faker->sentence(),
            'tags' => $this->faker->randomElements(Tag::values(), 2),
        ];
    }
}