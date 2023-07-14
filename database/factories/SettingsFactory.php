<?php

namespace Database\Factories;

use App\Models\Settings;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingsFactory extends Factory
{
    protected $model = Settings::class;

    public function definition(): array
    {
        return [
            'value' => $this->faker->word(),
        ];
    }
}
