<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $colors = collect(Status::$colors)->keys();
        $status = "Status  {$this->faker->unique()->randomNumber()}";

        return [
            'name' => $status,
            'color' => $this->faker->randomElement($colors),
        ];
    }
}
