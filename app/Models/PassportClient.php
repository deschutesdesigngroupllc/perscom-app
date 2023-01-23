<?php

namespace App\Models;

use Laravel\Passport\Client as BaseClientModel;

class PassportClient extends BaseClientModel
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
