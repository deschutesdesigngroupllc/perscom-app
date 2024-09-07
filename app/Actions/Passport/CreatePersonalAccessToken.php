<?php

declare(strict_types=1);

namespace App\Actions\Passport;

use App\Models\User;
use Laravel\Passport\PersonalAccessTokenResult;

class CreatePersonalAccessToken
{
    public function handle(User $user, string $name, array $scopes = []): PersonalAccessTokenResult
    {
        $token = $user->createToken($name, $scopes);
        $token->token->forceFill([
            'token' => $token->accessToken,
        ]);
        $token->token->save();

        return $token;
    }
}
