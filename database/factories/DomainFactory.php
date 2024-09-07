<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class DomainFactory extends Factory
{
    public function definition(): array
    {
        return [
            'domain' => $this->faker->domainWord,
        ];
    }
}
