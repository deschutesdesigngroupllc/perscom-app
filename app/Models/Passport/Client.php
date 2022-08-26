<?php

namespace App\Models\Passport;

use Laravel\Passport\Client as BaseClientModel;

class Client extends BaseClientModel
{
    /**
     * @var string[]
     */
    protected $attributes = [
        'personal_access_client' => false,
        'password_client' => false,
        'revoked' => false,
    ];
}
