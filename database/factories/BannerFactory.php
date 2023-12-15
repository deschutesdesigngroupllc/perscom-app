<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message' => $this->faker->sentence,
            'link_text' => $this->faker->word,
            'link_url' => $this->faker->url,
        ];
    }
}
