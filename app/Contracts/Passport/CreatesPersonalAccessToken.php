<?php

namespace App\Contracts\Passport;

use App\Models\User;
use Laravel\Passport\PersonalAccessTokenResult;

interface CreatesPersonalAccessToken
{
    /**
     * @param  array<int, string>  $scopes
     */
    public function create(User $user, string $name, array $scopes = []): PersonalAccessTokenResult;
}
