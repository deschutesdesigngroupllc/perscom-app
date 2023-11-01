<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Http\Middleware\CheckClientCredentials as BaseCheckClientCredentials;

class CheckClientCredentials extends BaseCheckClientCredentials
{
    // @phpstan-ignore-next-line
    protected function validate($psr, $scopes): void
    {
        parent::validate($psr, $scopes);

        Auth::shouldUse('api');
    }

    protected function validateCredentials($token): void
    {
        parent::validateCredentials($token);

        request()->attributes->add([
            'client_credentials_token' => $token,
        ]);
    }
}
