<?php

namespace Database\Factories;

use Codinglabs\FeatureFlags\Enums\FeatureState;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FeatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => Str::kebab($this->faker->word),
            'description' => $this->faker->sentence,
            'state' => $this->faker->randomElement([FeatureState::on(), FeatureState::off()]),
        ];
    }
}
