<?php

namespace App\Actions\Passport;

use App\Contracts\Passport\CreatesPersonalAccessToken;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class CreatePersonalAccessToken implements CreatesPersonalAccessToken
{
    /**
     * @param User  $user
     * @param       $name
     * @param array $scopes
     *
     * @return \Laravel\Passport\PersonalAccessTokenResult
     */
    public function create(User $user, $name, array $scopes = array())
    {
        $token = $user->createToken($name, $scopes);
        $token->token->forceFill([
            'token' => Crypt::encryptString($token->accessToken),
        ]);
        $token->token->save();

        return $token;
    }
}
