<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\ServiceRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attachment>
 */
class AttachmentFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterMaking(fn (Attachment $attachment) => $attachment->model()->associate(ServiceRecord::factory()->create()));
    }

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'filename' => $this->faker->word,
            'path' => $this->faker->filePath(),
        ];
    }
}
