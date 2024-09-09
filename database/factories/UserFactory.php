<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Position;
use App\Models\Rank;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole(Utils::getPanelUserRoleName()));
    }

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'position_id' => Position::factory(),
            'rank_id' => Rank::factory(),
            'specialty_id' => Specialty::factory(),
            'status_id' => Status::factory(),
            'unit_id' => Unit::factory(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'notes' => null,
            'notes_updated_at' => null,
            'profile_photo' => null,
            'cover_photo' => null,
            'last_seen_at' => null,
        ];
    }

    public function unverified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
