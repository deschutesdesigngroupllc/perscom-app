<?php

namespace Database\Factories;

use App\Models\Enums\WebhookEvent;
use App\Models\Enums\WebhookMethod;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Webhook>
 */
class WebhookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => $this->faker->url,
            'description' => $this->faker->sentence,
            'method' => $this->faker->randomElement(WebhookMethod::cases()),
            'events' => $this->faker->randomElements(WebhookEvent::cases(), 10),
            'secret' => Str::random(),
        ];
    }
}
