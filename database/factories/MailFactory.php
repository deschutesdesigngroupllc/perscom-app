<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Mail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mail>
 */
class MailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subject' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'send_now' => true,
        ];
    }
}
