<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Award;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Image>
 */
class ImageFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterMaking(fn (Image $image) => $image->model()->associate(Award::factory()->create()));
    }

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'filename' => $this->faker->word,
            'path' => $this->faker->filePath(),
        ];
    }
}
