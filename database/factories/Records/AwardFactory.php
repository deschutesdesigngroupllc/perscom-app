<?php

namespace Database\Factories\Records;

use App\Models\Award;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AwardFactory extends Factory
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
	        'person_id' => Person::factory(),
	        'award_id' => Award::factory()
        ];
    }
}
