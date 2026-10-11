<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Enums\InviteType;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Invite>
 */
class InviteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => InviteType::CREATE,
            'email' => $this->faker->unique()->safeEmail(),
            'name' => $this->faker->name(),
            'token' => Invite::hashToken(Str::random(64)),
            'sent_at' => now(),
            'expires_at' => now()->addDays(Invite::EXPIRATION_DAYS),
        ];
    }

    public function claim(?User $user = null): static
    {
        return $this->state(fn (): array => [
            'type' => InviteType::CLAIM,
            'user_id' => $user->id ?? User::factory(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (): array => [
            'expires_at' => now()->subDay(),
        ]);
    }

    public function revoked(): static
    {
        return $this->state(fn (): array => [
            'revoked_at' => now(),
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn (): array => [
            'accepted_at' => now(),
        ]);
    }

    public function withToken(string $token): static
    {
        return $this->state(fn (): array => [
            'token' => Invite::hashToken($token),
        ]);
    }
}
