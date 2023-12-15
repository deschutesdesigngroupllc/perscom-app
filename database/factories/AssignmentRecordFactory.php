<?php

namespace Database\Factories;

use App\Models\AssignmentRecord;
use App\Models\Document;
use App\Models\Position;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssignmentRecord>
 */
class AssignmentRecordFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status_id' => $this->faker->randomElement([Status::factory(), null]),
            'unit_id' => $this->faker->randomElement([Unit::factory(), null]),
            'position_id' => $this->faker->randomElement([Position::factory(), null]),
            'specialty_id' => $this->faker->randomElement([Specialty::factory(), null]),
            'document_id' => $this->faker->randomElement([Document::factory(), null]),
            'author_id' => User::factory(),
            'text' => $this->faker->sentence(),
        ];
    }
}
