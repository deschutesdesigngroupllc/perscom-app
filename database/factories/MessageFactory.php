<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->word,
            'message' => $this->faker->sentences(2),
            'link_text' => $this->faker->word,
            'url' => $this->faker->url,
            'active' => $this->faker->boolean,
        ];
    }
}
