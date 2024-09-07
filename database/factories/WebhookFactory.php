<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Enums\WebhookEvent;
use App\Models\Enums\WebhookMethod;
use App\Models\Webhook;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Webhook>
 */
class WebhookFactory extends Factory
{
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
