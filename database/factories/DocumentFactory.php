<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => "Document {$this->faker->unique()->randomNumber()}",
            'description' => $this->faker->paragraph,
            'content' => $this->faker->randomHtml,
        ];
    }
}
