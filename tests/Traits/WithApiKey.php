<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Actions\Passport\CreatePersonalAccessToken;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

trait WithApiKey
{
    use WithFaker;

    protected function apiKey(array|string $scopes = ['view:user'], ?User $user = null): string
    {
        $action = new CreatePersonalAccessToken;

        return $action->handle($user ?? User::factory()->unassigned()->createQuietly(), $this->faker->word, Arr::wrap($scopes))->accessToken;
    }
}
