<?php

namespace App\Contracts\Passport;

use App\Models\User;

interface CreatesPersonalAccessToken
{
    /**
     * @param User  $user
     * @param       $name
     * @param array $scopes
     *
     * @return mixed
     */
    public function create(User $user, $name, array $scopes = []);
}
