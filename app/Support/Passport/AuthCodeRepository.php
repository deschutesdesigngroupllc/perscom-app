<?php

declare(strict_types=1);

namespace App\Support\Passport;

use Illuminate\Support\Facades\Cache;
use Laravel\Passport\Bridge\AuthCodeRepository as PassportAuthCodeRepository;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class AuthCodeRepository extends PassportAuthCodeRepository
{
    /**
     * Persist the authorization code and bind the OpenID Connect nonce and authentication time of the
     * authorization request it was issued for, so the ID token issued in exchange for it carries them.
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        parent::persistNewAuthCode($authCodeEntity);

        $isOpenIdRequest = collect($authCodeEntity->getScopes())
            ->contains(fn (ScopeEntityInterface $scope): bool => $scope->getIdentifier() === 'openid');

        if (! $isOpenIdRequest) {
            return;
        }

        Cache::put(OidcAuthorization::authCodeCacheKey($authCodeEntity->getIdentifier()), [
            'nonce' => OidcAuthorization::nonce(),
            'auth_time' => OidcAuthorization::authTime(),
        ], $authCodeEntity->getExpiryDateTime());
    }
}
