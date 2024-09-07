<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'author_id' => User::factory(),
            'name' => "Document {$this->faker->unique()->randomNumber()}",
            'description' => $this->faker->paragraph,
            'content' => $this->faker->paragraph,
        ];
    }
}
