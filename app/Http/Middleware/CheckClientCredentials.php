<?php

namespace App\Http\Middleware;

class CheckClientCredentials extends \Laravel\Passport\Http\Middleware\CheckClientCredentials
{
    protected function validateCredentials($token)
    {
        request()->attributes->add([
            'client_credentials_token' => $token,
        ]);

        parent::validateCredentials($token);
    }
}
