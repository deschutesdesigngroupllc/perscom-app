<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Document;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QualificationRecord>
 */
class QualificationRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'qualification_id' => Qualification::factory(),
            'document_id' => $this->faker->randomElement([Document::factory(), null]),
            'author_id' => User::factory(),
            'text' => $this->faker->sentence(),
        ];
    }
}
