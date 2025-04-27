<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Competency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Competency>
 */
class CompetencyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $competency = "Competency {$this->faker->unique()->randomNumber()}";

        return [
            'name' => $competency,
        ];
    }
}
