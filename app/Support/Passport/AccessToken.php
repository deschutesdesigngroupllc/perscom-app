<?php

declare(strict_types=1);

namespace App\Support\Passport;

use App\Models\User;
use Carbon\CarbonImmutable;
use Laravel\Passport\Bridge\AccessToken as PassportAccessToken;
use Lcobucci\JWT\Token;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;

class AccessToken extends PassportAccessToken
{
    use AccessTokenTrait;

    private function convertToJWT(): Token
    {
        $this->initJwtConfiguration();

        $builder = $this->jwtConfiguration->builder()
            ->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier())
            ->issuedAt(CarbonImmutable::now())
            ->canOnlyBeUsedAfter(CarbonImmutable::now())
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo((string) $this->getUserIdentifier())
            ->withClaim('scopes', $this->getScopes());

        if ($user = User::find($this->getUserIdentifier())) {
            foreach ($user->getJWTCustomClaims() as $key => $value) {
                $builder = $builder->withClaim($key, $value);
            }
        }

        return $builder->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey());
    }
}
