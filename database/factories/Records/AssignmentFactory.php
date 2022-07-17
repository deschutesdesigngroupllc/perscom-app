<?php

namespace Database\Factories\Records;

use App\Models\Document;
use App\Models\Position;
use App\Models\Specialty;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
	        'text' => $this->faker->sentence(),
	        'author_id' => User::factory(),
	        'user_id' => User::factory(),
	        'unit_id' => Unit::factory(),
	        'position_id' => Position::factory(),
	        'specialty_id' => Specialty::factory(),
	        'document_id' => Document::factory()
        ];
    }
}
