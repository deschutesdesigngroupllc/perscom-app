<?php

namespace Database\Factories;

use App\Models\Mail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mail>
 */
class MailFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'send_now' => true,
        ];
    }
}
