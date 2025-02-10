<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Position;
use App\Models\Slot;
use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Slot>
 */
class SlotFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => "Slot {$this->faker->unique()->randomNumber()}",
            'position_id' => Position::factory(),
            'specialty_id' => Specialty::factory(),
            'description' => $this->faker->sentence,
            'empty' => $this->faker->sentence,
            'hidden' => false,
        ];
    }
}
