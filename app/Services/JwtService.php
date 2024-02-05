<?php

namespace App\Services;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use PHPOpenSourceSaver\JWTAuth\Token;

class JwtService
{
    public static function signedByPerscom(?Token $token): bool
    {
        if (is_null($token)) {
            return false;
        }

        return rescue(function () use ($token) {
            $provider = app()->make('tymon.jwt.provider.jwt.lcobucci');

            $config = Configuration::forSymmetricSigner($provider->getPerscomSigner(), InMemory::plainText(config('jwt.secret')));
            $config->setValidationConstraints(new SignedWith($provider->getPerscomSigner(), InMemory::plainText(config('jwt.secret'))));

            $jwt = $config->parser()->parse($token->get());

            if ($config->validator()->validate($jwt, ...$config->validationConstraints())) {
                return true;
            }

            return false;
        }, false);
    }
}
