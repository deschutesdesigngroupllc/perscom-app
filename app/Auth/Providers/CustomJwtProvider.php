<?php

namespace App\Auth\Providers;

use PHPOpenSourceSaver\JWTAuth\Providers\JWT\Lcobucci;

class CustomJwtProvider extends Lcobucci
{
    public function __construct($secret, $algo, array $keys, $config = null)
    {
        $secret = setting('single_sign_on_key', $secret);

        parent::__construct($secret, $algo, $keys, $config);
    }
}
