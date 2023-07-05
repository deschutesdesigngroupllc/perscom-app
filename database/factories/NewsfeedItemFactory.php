<?php

namespace Database\Factories;

use App\Models\NewsfeedItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class NewsfeedItemFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = NewsfeedItem::class;

    /**
     * @return array|mixed[]
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'batch_uuid' => $this->faker->uuid(),
            'subject_id' => $this->faker->randomNumber(),
            'causer_type' => $this->faker->word(),
            'updated_at' => Carbon::now(),
            'subject_type' => $this->faker->word(),
            'log_name' => $this->faker->name(),
            'causer_id' => $this->faker->randomNumber(),
            'properties' => $this->faker->word(),
            'event' => $this->faker->word(),
        ];
    }
}
