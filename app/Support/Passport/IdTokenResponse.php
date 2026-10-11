<?php

declare(strict_types=1);

namespace App\Support\Passport;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use SensitiveParameter;

class IdTokenResponse extends BearerTokenResponse
{
    /**
     * The cache key prefix used to hand the authorization request's nonce to the token response.
     */
    public const string NONCE_CACHE_PREFIX = 'oidc:authorization';

    public static function authorizationCacheKey(string $clientId, string $userId): string
    {
        return sprintf('%s:%s:%s', static::NONCE_CACHE_PREFIX, $clientId, $userId);
    }

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

        /** @var array{nonce?: string|null, auth_time?: int}|null $authorization */
        $authorization = Cache::pull(static::authorizationCacheKey($clientId, (string) $user->getKey()));

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
            ->expiresAt(min($accessToken->getExpiryDateTime(), now()->addHour()->toImmutable()))
            ->withClaim('auth_time', $authorization['auth_time'] ?? now()->getTimestamp());

        if (filled($authorization['nonce'] ?? null)) {
            $builder = $builder->withClaim('nonce', $authorization['nonce']);
        }

        foreach (OidcClaims::forScopes($user, $scopes) as $key => $value) {
            $builder = $builder->withClaim($key, $value);
        }

        return $builder->getToken($configuration->signer(), $configuration->signingKey())->toString();
    }

    protected function issuer(): string
    {
        $tenant = tenant();

        return $tenant instanceof Tenant
            ? $tenant->url
            : (string) config('app.url');
    }
}
