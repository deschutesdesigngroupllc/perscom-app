<?php

namespace App\Contracts\Passport;

use App\Models\User;

interface CreatesPersonalAccessToken
{
    /**
     * @return mixed
     */
    public function create(User $user, $name, array $scopes = []);
}
