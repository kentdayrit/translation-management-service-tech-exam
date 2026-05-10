<?php

namespace Database\Seeders;

use App\Enums\Locale;
use App\Enums\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('translations')->truncate();
        Schema::enableForeignKeyConstraints();

        $total = 100000;
        $chunkSize = 1000;
        $locales = Locale::values();
        $now = now();
        $faker = \Faker\Factory::create();

        for ($offset = 0; $offset < $total; $offset += $chunkSize) {
            $batch = [];

            for ($i = 0; $i < $chunkSize; $i++) {
                $index = $offset + $i + 1;

                $batch[] = [
                    'key' => "app.label.{$index}",
                    'locale' => $locales[$index % count($locales)],
                    'content' => $faker->sentence(),
                    'tags' => json_encode(Tag::values()),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('translations')->insert($batch);
        }

        $this->command->info("Successfully seeded {$total} translations.");
    }
}
