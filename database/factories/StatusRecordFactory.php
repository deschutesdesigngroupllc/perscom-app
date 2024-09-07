<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Status;
use App\Models\StatusRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StatusRecord>
 */
class StatusRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status_id' => Status::factory(),
        ];
    }
}
