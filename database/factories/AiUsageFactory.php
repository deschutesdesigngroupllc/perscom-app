<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiUsage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiUsage>
 */
class AiUsageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invocation_id' => $this->faker->uuid(),
            'agent' => 'App\\Ai\\Agents\\TestAgent',
            'provider' => 'anthropic',
            'model' => 'claude-sonnet-5-5',
            'input_tokens' => $this->faker->numberBetween(100, 5000),
            'output_tokens' => $this->faker->numberBetween(100, 5000),
        ];
    }
}
