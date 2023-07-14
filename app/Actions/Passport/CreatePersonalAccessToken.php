<?php

namespace App\Actions\Passport;

use App\Contracts\Passport\CreatesPersonalAccessToken;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Laravel\Passport\PersonalAccessTokenResult;

class CreatePersonalAccessToken implements CreatesPersonalAccessToken
{
    /**
     * @param  array<string>  $scopes
     */
    public function create(User $user, string $name, array $scopes = []): PersonalAccessTokenResult
    {
        $token = $user->createToken($name, $scopes);
        $token->token->forceFill([
            'token' => Crypt::encryptString($token->accessToken),
        ]);
        $token->token->save();

        return $token;
    }
}
