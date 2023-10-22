<?php

namespace App\Contracts\Passport;

use App\Models\User;
use Laravel\Passport\PersonalAccessTokenResult;

interface CreatesPersonalAccessToken
{
    public function create(User $user, $name, array $scopes = []): PersonalAccessTokenResult;
}
