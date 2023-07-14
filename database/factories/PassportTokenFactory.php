<?php

namespace Database\Factories;

use App\Models\PassportToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PassportTokenFactory extends Factory
{
    protected $model = PassportToken::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'client_id' => $this->faker->word(),
            'name' => $this->faker->name(),
            'scopes' => $this->faker->words(),
            'token' => Str::random(10),
            'revoked' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'expires_at' => Carbon::now(),
        ];
    }
}
