<?php

declare(strict_types=1);

namespace App\Support\Passport;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use LogicException;
use SensitiveParameter;

class IdTokenResponse extends BearerTokenResponse
{
    /**
     * @return array<string, string>
     */
    protected function getExtraParams(
        #[SensitiveParameter]
        AccessTokenEntityInterface $accessToken
    ): array {
        $scopes = array_map(fn (ScopeEntityInterface $scope): string => $scope->getIdentifier(), $accessToken->getScopes());

        if (! in_array('openid', $scopes, true) || blank($accessToken->getUserIdentifier())) {
            return [];
        }

        $user = User::find($accessToken->getUserIdentifier());

        if (! $user instanceof User) {
            return [];
        }

        return [
            'id_token' => $this->makeIdToken($accessToken, $user, $scopes),
        ];
    }

    /**
     * @param  array<int, string>  $scopes
     */
    protected function makeIdToken(AccessTokenEntityInterface $accessToken, User $user, array $scopes): string
    {
        $clientId = $accessToken->getClient()->getIdentifier();

        $authorization = $this->authorization($accessToken);

        $configuration = Configuration::forAsymmetricSigner(
            new Sha256,
            InMemory::plainText($this->privateKey->getKeyContents(), $this->privateKey->getPassPhrase() ?? ''),
            InMemory::plainText('empty', 'empty')
        );

        $builder = $configuration->builder()
            ->withHeader('kid', JsonWebKey::thumbprint())
            ->issuedBy($this->issuer())
            ->permittedFor($clientId)
            ->relatedTo((string) $user->getKey())
            ->issuedAt(now()->toImmutable())
            ->expiresAt(min($accessToken->getExpiryDateTime(), now()->addHour()->toImmutable()));

        if (filled($authorization['auth_time'] ?? null)) {
            $builder = $builder->withClaim('auth_time', $authorization['auth_time']);
        }

        if (filled($authorization['nonce'] ?? null)) {
            $builder = $builder->withClaim('nonce', $authorization['nonce']);
        }

        foreach (OidcClaims::forScopes($user, $scopes) as $key => $value) {
            $builder = $builder->withClaim($key, $value);
        }

        return $builder->getToken($configuration->signer(), $configuration->signingKey())->toString();
    }

    /**
     * Resolve the nonce and authentication time of the authorization the access token was issued for.
     * Authorization codes carry the values bound to their authorization request, refresh tokens carry
     * the authentication time of the original authorization, and the password grant authenticates the
     * user with their credentials as the token is issued.
     *
     * @return array{nonce?: string|null, auth_time?: int|null}
     */
    protected function authorization(AccessTokenEntityInterface $accessToken): array
    {
        /** @var array{nonce?: string|null, auth_time?: int|null} $authorization */
        $authorization = match (request()->input('grant_type')) {
            'authorization_code' => Cache::pull(OidcAuthorization::authCodeCacheKey((string) ($this->decryptPayload('code')['auth_code_id'] ?? ''))) ?? [],
            'refresh_token' => Arr::only(Cache::get(OidcAuthorization::accessTokenCacheKey((string) ($this->decryptPayload('refresh_token')['access_token_id'] ?? ''))) ?? [], ['auth_time']),
            'password' => ['auth_time' => now()->getTimestamp()],
            default => [],
        };

        if (isset($this->refreshToken) && filled($authorization['auth_time'] ?? null)) {
            Cache::put(
                OidcAuthorization::accessTokenCacheKey($accessToken->getIdentifier()),
                ['auth_time' => $authorization['auth_time']],
                $this->refreshToken->getExpiryDateTime()
            );
        }

        return $authorization;
    }

    /**
     * Decrypt the payload of the authorization code or refresh token the token request was made with.
     *
     * @return array<string, mixed>
     */
    protected function decryptPayload(string $parameter): array
    {
        $value = request()->input($parameter);

        if (! is_string($value) || blank($value)) {
            return [];
        }

        try {
            $payload = json_decode($this->decrypt($value), true);
        } catch (LogicException) {
            return [];
        }

        return is_array($payload) ? $payload : [];
    }

    protected function issuer(): string
    {
        $tenant = tenant();

        return $tenant instanceof Tenant
            ? $tenant->url
            : (string) config('app.url');
    }
}
