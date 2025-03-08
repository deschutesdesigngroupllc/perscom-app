<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Passport;

use App\Http\Middleware\CheckSubscription;
use App\Models\User;
use DateInterval;
use DateTimeImmutable;
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
        $this->encryptionKey = app('encrypter')->getKey();

        $this->withoutMiddleware(CheckSubscription::class);

        $user = User::factory()->createQuietly();

        $client = ClientFactory::new()->create([
            'user_id' => $user->getKey(),
            'redirect' => $redirect = $this->faker->url,
        ]);

        $authCodeRepository = $this->app->make(AuthCodeRepository::class);
        $clientRepository = $this->app->make(ClientRepository::class);
        $scopeRepository = $this->app->make(ScopeRepository::class);

        $authCode = $authCodeRepository->getNewAuthCode();
        $authCode->setClient($clientRepository->getClientEntity($client->getKey()));
        $authCode->setUserIdentifier($user->getKey());
        $authCode->setRedirectUri($redirect);
        $authCode->addScope($scopeRepository->getScopeEntityByIdentifier('view:user'));

        $payload = [
            'client_id' => $authCode->getClient()->getIdentifier(),
            'redirect_uri' => $authCode->getRedirectUri(),
            'auth_code_id' => $authCode->getIdentifier(),
            'scopes' => $authCode->getScopes(),
            'user_id' => $authCode->getUserIdentifier(),
            'expire_time' => (new DateTimeImmutable)->add(new DateInterval('PT10M'))->getTimestamp(),
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
        $this->encryptionKey = app('encrypter')->getKey();

        $this->withoutMiddleware(CheckSubscription::class);

        $user = User::factory()->createQuietly();

        $client = ClientFactory::new()->create([
            'user_id' => $user->getKey(),
            'redirect' => $redirect = $this->faker->url,
        ]);

        $authCodeRepository = $this->app->make(AuthCodeRepository::class);
        $clientRepository = $this->app->make(ClientRepository::class);
        $scopeRepository = $this->app->make(ScopeRepository::class);

        $authCode = $authCodeRepository->getNewAuthCode();
        $authCode->setClient($clientRepository->getClientEntity($client->getKey()));
        $authCode->setUserIdentifier($user->getKey());
        $authCode->setRedirectUri($redirect);
        $authCode->addScope($scopeRepository->getScopeEntityByIdentifier('view:user'));

        $payload = [
            'client_id' => $authCode->getClient()->getIdentifier(),
            'redirect_uri' => $authCode->getRedirectUri(),
            'auth_code_id' => $authCode->getIdentifier(),
            'scopes' => $authCode->getScopes(),
            'user_id' => $authCode->getUserIdentifier(),
            'expire_time' => (new DateTimeImmutable)->add(new DateInterval('PT10M'))->getTimestamp(),
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
            ]);
    }
}
