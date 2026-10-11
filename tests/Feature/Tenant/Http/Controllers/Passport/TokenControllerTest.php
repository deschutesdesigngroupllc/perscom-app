<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Passport;

use App\Http\Middleware\CheckSubscription;
use App\Models\User;
use App\Support\Passport\IdTokenResponse;
use Carbon\CarbonImmutable;
use DateInterval;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Passport\Bridge\AuthCodeRepository;
use Laravel\Passport\Bridge\ClientRepository;
use Laravel\Passport\Bridge\ScopeRepository;
use Laravel\Passport\Database\Factories\ClientFactory;
use League\OAuth2\Server\CryptTrait;
use Tests\Feature\Tenant\TenantTestCase;

class TokenControllerTest extends TenantTestCase
{
    use CryptTrait;

    public function test_token_from_authorization_code_grant_can_be_retrieved(): void
    {
        $this->encryptionKey = resolve('encrypter')->getKey();

        $this->withoutMiddleware(CheckSubscription::class);

        $user = User::factory()->createQuietly();

        $redirect = $this->faker->url;

        $client = ClientFactory::new()->create([
            'owner_id' => $user->getKey(),
            'owner_type' => $user->getMorphClass(),
            'redirect_uris' => [$redirect],
        ]);

        $authCodeRepository = $this->app->make(AuthCodeRepository::class);
        $clientRepository = $this->app->make(ClientRepository::class);
        $scopeRepository = $this->app->make(ScopeRepository::class);

        $authCode = $authCodeRepository->getNewAuthCode();
        $authCode->setIdentifier(Str::random(40));
        $authCode->setClient($clientRepository->getClientEntity($client->getKey()));
        $authCode->setUserIdentifier((string) $user->getKey());
        $authCode->setRedirectUri($redirect);
        $authCode->setExpiryDateTime(CarbonImmutable::now()->add(new DateInterval('PT10M')));
        $authCode->addScope($scopeRepository->getScopeEntityByIdentifier('view:user'));

        $authCodeRepository->persistNewAuthCode($authCode);

        $payload = [
            'client_id' => $authCode->getClient()->getIdentifier(),
            'redirect_uri' => $authCode->getRedirectUri(),
            'auth_code_id' => $authCode->getIdentifier(),
            'scopes' => $authCode->getScopes(),
            'user_id' => $authCode->getUserIdentifier(),
            'expire_time' => (CarbonImmutable::now())->add(new DateInterval('PT10M'))->getTimestamp(),
        ];

        $this->postJson($this->tenant->route('passport.token'), [
            'grant_type' => 'authorization_code',
            'code' => $this->encrypt(json_encode($payload)),
            'client_id' => $client->getKey(),
            'client_secret' => $client->plainSecret,
            'redirect_uri' => $redirect,
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'token_type',
                'expires_in',
                'access_token',
                'refresh_token',
            ]);
    }

    public function test_token_from_refresh_token_grant_can_be_retrieved(): void
    {
        $this->encryptionKey = resolve('encrypter')->getKey();

        $this->withoutMiddleware(CheckSubscription::class);

        $user = User::factory()->createQuietly();

        $redirect = $this->faker->url;

        $client = ClientFactory::new()->create([
            'owner_id' => $user->getKey(),
            'owner_type' => $user->getMorphClass(),
            'redirect_uris' => [$redirect],
        ]);

        $authCodeRepository = $this->app->make(AuthCodeRepository::class);
        $clientRepository = $this->app->make(ClientRepository::class);
        $scopeRepository = $this->app->make(ScopeRepository::class);

        $authCode = $authCodeRepository->getNewAuthCode();
        $authCode->setIdentifier(Str::random(40));
        $authCode->setClient($clientRepository->getClientEntity($client->getKey()));
        $authCode->setUserIdentifier((string) $user->getKey());
        $authCode->setRedirectUri($redirect);
        $authCode->setExpiryDateTime(CarbonImmutable::now()->add(new DateInterval('PT10M')));
        $authCode->addScope($scopeRepository->getScopeEntityByIdentifier('view:user'));

        $authCodeRepository->persistNewAuthCode($authCode);

        $payload = [
            'client_id' => $authCode->getClient()->getIdentifier(),
            'redirect_uri' => $authCode->getRedirectUri(),
            'auth_code_id' => $authCode->getIdentifier(),
            'scopes' => $authCode->getScopes(),
            'user_id' => $authCode->getUserIdentifier(),
            'expire_time' => (CarbonImmutable::now())->add(new DateInterval('PT10M'))->getTimestamp(),
        ];

        $refreshToken = $this->postJson($this->tenant->route('passport.token'), [
            'grant_type' => 'authorization_code',
            'code' => $this->encrypt(json_encode($payload)),
            'client_id' => $client->getKey(),
            'client_secret' => $client->plainSecret,
            'redirect_uri' => $redirect,
        ])->json('refresh_token');

        $this->postJson($this->tenant->route('passport.token'), [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $client->getKey(),
            'client_secret' => $client->plainSecret,
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'token_type',
                'expires_in',
                'access_token',
            ]);
    }

    public function test_token_from_client_credentials_grant_can_be_retrieved(): void
    {
        $this->withoutMiddleware(CheckSubscription::class);

        $client = ClientFactory::new()->asClientCredentials()->create();

        $this->postJson($this->tenant->route('passport.token'), [
            'grant_type' => 'client_credentials',
            'client_id' => $client->getKey(),
            'client_secret' => $client->plainSecret,
            'scope' => 'view:user',
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'token_type',
                'expires_in',
                'access_token',
            ]);
    }

    public function test_token_from_password_grant_can_be_retrieved(): void
    {
        $this->withoutMiddleware(CheckSubscription::class);

        $client = ClientFactory::new()->asPasswordClient()->create();

        $user = User::factory()->state([
            'password' => $password = $this->faker->password,
        ])->createQuietly();

        $this->postJson($this->tenant->route('passport.token'), [
            'grant_type' => 'password',
            'client_id' => $client->getKey(),
            'client_secret' => $client->plainSecret,
            'username' => $user->email,
            'password' => $password,
            'scope' => 'view:user',
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'token_type',
                'expires_in',
                'access_token',
            ])
            ->assertJsonMissingPath('id_token');
    }

    public function test_password_grant_with_openid_scope_returns_an_id_token_verifiable_with_the_jwks(): void
    {
        $this->withoutMiddleware(CheckSubscription::class);

        $client = ClientFactory::new()->asPasswordClient()->create();

        $user = User::factory()->state([
            'password' => $password = $this->faker->password,
        ])->createQuietly();

        $response = $this->postJson($this->tenant->route('passport.token'), [
            'grant_type' => 'password',
            'client_id' => $client->getKey(),
            'client_secret' => $client->plainSecret,
            'username' => $user->email,
            'password' => $password,
            'scope' => 'openid email',
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'access_token',
                'id_token',
            ]);

        $jwks = $this->get($this->tenant->route('oidc.jwks'))->json();

        $claims = (array) JWT::decode($response->json('id_token'), JWK::parseKeySet($jwks));

        $this->assertSame($this->tenant->url, $claims['iss']);
        $this->assertSame((string) $client->getKey(), $claims['aud']);
        $this->assertSame((string) $user->getKey(), $claims['sub']);
        $this->assertSame($user->email, $claims['email']);
        $this->assertArrayHasKey('auth_time', $claims);
        $this->assertArrayNotHasKey('name', $claims);
        $this->assertArrayNotHasKey('nonce', $claims);

        $accessToken = JWT::decode($response->json('access_token'), JWK::parseKeySet($jwks));

        $this->assertSame((string) $user->getKey(), $accessToken->sub);
    }

    public function test_authorization_code_grant_id_token_includes_the_authorization_request_nonce(): void
    {
        $this->encryptionKey = resolve('encrypter')->getKey();

        $this->withoutMiddleware(CheckSubscription::class);

        $user = User::factory()->createQuietly();

        $redirect = $this->faker->url;

        $client = ClientFactory::new()->create([
            'owner_id' => $user->getKey(),
            'owner_type' => $user->getMorphClass(),
            'redirect_uris' => [$redirect],
        ]);

        Cache::put(IdTokenResponse::authorizationCacheKey((string) $client->getKey(), (string) $user->getKey()), [
            'nonce' => 'nonce-value',
            'auth_time' => $authTime = now()->subMinute()->getTimestamp(),
        ]);

        $authCodeRepository = $this->app->make(AuthCodeRepository::class);
        $clientRepository = $this->app->make(ClientRepository::class);
        $scopeRepository = $this->app->make(ScopeRepository::class);

        $authCode = $authCodeRepository->getNewAuthCode();
        $authCode->setIdentifier(Str::random(40));
        $authCode->setClient($clientRepository->getClientEntity($client->getKey()));
        $authCode->setUserIdentifier((string) $user->getKey());
        $authCode->setRedirectUri($redirect);
        $authCode->setExpiryDateTime(CarbonImmutable::now()->add(new DateInterval('PT10M')));
        $authCode->addScope($scopeRepository->getScopeEntityByIdentifier('openid'));
        $authCode->addScope($scopeRepository->getScopeEntityByIdentifier('profile'));

        $authCodeRepository->persistNewAuthCode($authCode);

        $payload = [
            'client_id' => $authCode->getClient()->getIdentifier(),
            'redirect_uri' => $authCode->getRedirectUri(),
            'auth_code_id' => $authCode->getIdentifier(),
            'scopes' => $authCode->getScopes(),
            'user_id' => $authCode->getUserIdentifier(),
            'expire_time' => (CarbonImmutable::now())->add(new DateInterval('PT10M'))->getTimestamp(),
        ];

        $response = $this->postJson($this->tenant->route('passport.token'), [
            'grant_type' => 'authorization_code',
            'code' => $this->encrypt(json_encode($payload)),
            'client_id' => $client->getKey(),
            'client_secret' => $client->plainSecret,
            'redirect_uri' => $redirect,
        ])
            ->assertSuccessful()
            ->assertJsonStructure(['id_token']);

        $claims = (array) JWT::decode($response->json('id_token'), JWK::parseKeySet($this->get($this->tenant->route('oidc.jwks'))->json()));

        $this->assertSame('nonce-value', $claims['nonce']);
        $this->assertSame($authTime, $claims['auth_time']);
        $this->assertSame($user->name, $claims['name']);
        $this->assertArrayNotHasKey('email', $claims);
        $this->assertNull(Cache::get(IdTokenResponse::authorizationCacheKey((string) $client->getKey(), (string) $user->getKey())));
    }
}
