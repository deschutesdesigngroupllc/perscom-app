<?php

namespace Tests\Feature\Tenant\Http\Controllers\Passport;

use App\Http\Middleware\Subscribed;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateInterval;
use DateTimeImmutable;
use Illuminate\Contracts\Hashing\Hasher;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Database\Factories\ClientFactory;
use Laravel\Passport\PersonalAccessTokenFactory;
use Laravel\Passport\Token;
use Mockery\MockInterface;
use Spatie\Url\Url;
use Tests\Feature\Tenant\TenantTestCase;

class AccessTokenControllerTest extends TenantTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(Subscribed::class);
    }

    public function test_access_token_with_authorization_code_grant_can_be_issued()
    {
        $client = TestClientFactory::new()->create(['user_id' => $this->user->getKey()]);
        $clientRepository = $this->partialMock(ClientRepository::class, function (MockInterface $mock) use ($client) {
            $mock->shouldReceive('find')->andReturn($client);
        });
        $this->instance(ClientRepository::class, $clientRepository);

        $authorizationUrl = Url::fromString($this->tenant->url.'/oauth/authorize')->withQueryParameters([
            'response_type' => 'code',
            'client_id' => $client->id,
            'state' => 'test',
            'redirect_url' => $client->redirect,
            'prompt' => 'none',
            'scope' => 'view:user',
        ])->__toString();

        $authorizationResponse = $this->actingAs($this->user)
            ->get($authorizationUrl)
            ->assertFound()
            ->assertHeader('location');

        $redirectUrl = Url::fromString($authorizationResponse->headers->get('location'));

        $tokenResponse = $this->post($this->tenant->url.'/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $redirectUrl->getQueryParameter('code'),
            'redirect_url' => $client->redirect,
            'client_id' => $client->id,
            'client_secret' => $client->secret,
        ]);

        $tokenResponse->assertSuccessful();

        $tokenResponse->assertHeader('pragma', 'no-cache');
        $tokenResponse->assertHeader('cache-control', 'no-store, private');
        $tokenResponse->assertHeader('content-type', 'application/json; charset=UTF-8');

        $decodedTokenResponse = $tokenResponse->decodeResponseJson()->json();

        $this->assertArrayHasKey('token_type', $decodedTokenResponse);
        $this->assertArrayHasKey('expires_in', $decodedTokenResponse);
        $this->assertArrayHasKey('access_token', $decodedTokenResponse);
        $this->assertArrayHasKey('refresh_token', $decodedTokenResponse);
        $this->assertArrayNotHasKey('id_token', $decodedTokenResponse);
        $this->assertSame('Bearer', $decodedTokenResponse['token_type']);
        $expiresInSeconds = 31622400;
        $this->assertEqualsWithDelta($expiresInSeconds, $decodedTokenResponse['expires_in'], 5);

        $token = $this->app->make(PersonalAccessTokenFactory::class)->findAccessToken($decodedTokenResponse);
        $this->assertInstanceOf(Token::class, $token);
        $this->assertFalse($token->revoked);
        $this->assertTrue($token->client->is($client));
        $this->assertTrue($token->user->is($this->user));
        $this->assertNull($token->name);
        $this->assertLessThanOrEqual(5, CarbonImmutable::now()->addSeconds($expiresInSeconds)->diffInSeconds($token->expires_at));
    }

    public function test_id_token_with_authorization_code_grant_can_be_issued()
    {
        $client = TestClientFactory::new()->create(['user_id' => $this->user->getKey()]);
        $clientRepository = $this->partialMock(ClientRepository::class, function (MockInterface $mock) use ($client) {
            $mock->shouldReceive('find')->andReturn($client);
        });
        $this->instance(ClientRepository::class, $clientRepository);

        $authorizationUrl = Url::fromString($this->tenant->url.'/oauth/authorize')->withQueryParameters([
            'response_type' => 'code',
            'client_id' => $client->id,
            'state' => 'test',
            'redirect_url' => $client->redirect,
            'prompt' => 'none',
            'scope' => 'openid profile email tenant',
        ])->__toString();

        $authorizationResponse = $this->actingAs($this->user)
            ->get($authorizationUrl)
            ->assertFound()
            ->assertHeader('location');

        $redirectUrl = Url::fromString($authorizationResponse->headers->get('location'));

        $tokenResponse = $this->actingAs($this->user)
            ->post($this->tenant->url.'/oauth/token', [
                'grant_type' => 'authorization_code',
                'code' => $redirectUrl->getQueryParameter('code'),
                'redirect_url' => $client->redirect,
                'client_id' => $client->id,
                'client_secret' => $client->secret,
            ]);

        $tokenResponse->assertSuccessful();

        $decodedTokenResponse = $tokenResponse->decodeResponseJson()->json();

        $this->assertArrayHasKey('token_type', $decodedTokenResponse);
        $this->assertArrayHasKey('expires_in', $decodedTokenResponse);
        $this->assertArrayHasKey('access_token', $decodedTokenResponse);
        $this->assertArrayHasKey('refresh_token', $decodedTokenResponse);
        $this->assertArrayHasKey('id_token', $decodedTokenResponse);

        $tokenPayload = json_decode(base64_decode(explode('.', $decodedTokenResponse['id_token'])[1]), true);

        $this->assertArrayHasKey('aud', $tokenPayload);
        $this->assertArrayHasKey('iss', $tokenPayload);
        $this->assertArrayHasKey('iat', $tokenPayload);
        $this->assertArrayHasKey('exp', $tokenPayload);
        $this->assertArrayHasKey('sub', $tokenPayload);
        $this->assertArrayHasKey('name', $tokenPayload);
        $this->assertArrayHasKey('preferred_username', $tokenPayload);
        $this->assertArrayHasKey('profile', $tokenPayload);
        $this->assertArrayHasKey('picture', $tokenPayload);
        $this->assertArrayHasKey('locale', $tokenPayload);
        $this->assertArrayHasKey('zoneinfo', $tokenPayload);
        $this->assertArrayHasKey('updated_at', $tokenPayload);
        $this->assertArrayHasKey('email', $tokenPayload);
        $this->assertArrayHasKey('email_verified', $tokenPayload);
        $this->assertArrayHasKey('tenant_name', $tokenPayload);
        $this->assertArrayHasKey('tenant_sub', $tokenPayload);

        $issuedAt = new DateTimeImmutable();

        $this->assertSame($tokenPayload['aud'], $client->getKey());
        $this->assertSame($tokenPayload['iss'], 'https://'.$_SERVER['HTTP_HOST']);
        $this->assertEqualsWithDelta($tokenPayload['iat'], $issuedAt->getTimestamp(), 5);
        $this->assertEqualsWithDelta($tokenPayload['exp'], $issuedAt->add((new DateInterval('PT1H')))->getTimestamp(), 5);
        $this->assertSame($tokenPayload['sub'], (string) $this->user->getKey());
        $this->assertSame($tokenPayload['name'], $this->user->name);
        $this->assertSame($tokenPayload['preferred_username'], $this->user->email);
        $this->assertSame($tokenPayload['profile'], $this->user->url);
        $this->assertSame($tokenPayload['picture'], $this->user->profile_photo_url);
        $this->assertSame($tokenPayload['locale'], config('app.locale'));
        $this->assertSame($tokenPayload['zoneinfo'], config('app.timezone'));
        $this->assertSame($tokenPayload['updated_at'], Carbon::parse($this->user->updated_at)->getTimestamp());
        $this->assertSame($tokenPayload['email'], $this->user->email);
        $this->assertSame($tokenPayload['email_verified'], $this->user->hasVerifiedEmail());
        $this->assertSame($tokenPayload['tenant_name'], $this->tenant->name);
        $this->assertSame($tokenPayload['tenant_sub'], $this->tenant->getKey());
    }

    public function test_access_token_with_password_grant_can_be_issued()
    {
        $this->user->password = $this->app->make(Hasher::class)->make('foobar123');
        $this->user->save();

        $client = ClientFactory::new()->asPasswordClient()->create(['user_id' => $this->user->getKey()]);

        $response = $this->post('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $client->getKey(),
            'client_secret' => $client->secret,
            'username' => $this->user->email,
            'password' => 'foobar123',
        ]);

        $response->assertSuccessful();

        $response->assertHeader('pragma', 'no-cache');
        $response->assertHeader('cache-control', 'no-store, private');
        $response->assertHeader('content-type', 'application/json; charset=UTF-8');

        $decodedResponse = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('token_type', $decodedResponse);
        $this->assertArrayHasKey('expires_in', $decodedResponse);
        $this->assertArrayHasKey('access_token', $decodedResponse);
        $this->assertSame('Bearer', $decodedResponse['token_type']);
        $expiresInSeconds = 31622400;
        $this->assertEqualsWithDelta($expiresInSeconds, $decodedResponse['expires_in'], 5);

        $token = $this->app->make(PersonalAccessTokenFactory::class)->findAccessToken($decodedResponse);
        $this->assertInstanceOf(Token::class, $token);
        $this->assertFalse($token->revoked);
        $this->assertTrue($token->user->is($this->user));
        $this->assertTrue($token->client->is($client));
        $this->assertNull($token->name);
        $this->assertLessThanOrEqual(5, CarbonImmutable::now()->addSeconds($expiresInSeconds)->diffInSeconds($token->expires_at));
    }

    public function test_access_token_with_client_credentials_grant_can_be_issued()
    {
        $client = ClientFactory::new()->asClientCredentials()->create(['user_id' => $this->user->getKey()]);

        $response = $this->post('/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $client->getKey(),
            'client_secret' => $client->secret,
        ]);

        $response->assertSuccessful();

        $response->assertHeader('pragma', 'no-cache');
        $response->assertHeader('cache-control', 'no-store, private');
        $response->assertHeader('content-type', 'application/json; charset=UTF-8');

        $decodedResponse = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('token_type', $decodedResponse);
        $this->assertArrayHasKey('expires_in', $decodedResponse);
        $this->assertArrayHasKey('access_token', $decodedResponse);
        $expiresInSeconds = 31622400;
        $this->assertEqualsWithDelta($expiresInSeconds, $decodedResponse['expires_in'], 5);

        $token = $this->app->make(PersonalAccessTokenFactory::class)->findAccessToken($decodedResponse);
        $this->assertInstanceOf(Token::class, $token);
        $this->assertFalse($token->revoked);
        $this->assertTrue($token->client->is($client));
        $this->assertNull($token->user_id);
        $this->assertNull($token->name);
        $this->assertLessThanOrEqual(5, CarbonImmutable::now()->addSeconds($expiresInSeconds)->diffInSeconds($token->expires_at));
    }

    public function test_access_token_with_implicit_grant_can_be_issued()
    {
        $client = TestClientFactory::new()->create(['user_id' => $this->user->getKey()]);
        $clientRepository = $this->partialMock(ClientRepository::class, function (MockInterface $mock) use ($client) {
            $mock->shouldReceive('find')->andReturn($client);
        });
        $this->instance(ClientRepository::class, $clientRepository);

        $authorizationUrl = Url::fromString($this->tenant->url.'/oauth/authorize')->withQueryParameters([
            'response_type' => 'token',
            'client_id' => $client->id,
            'redirect_url' => $client->redirect,
            'scope' => 'view:user',
        ])->__toString();

        $authorizationResponse = $this->actingAs($this->user)
            ->get($authorizationUrl)
            ->assertFound()
            ->assertHeader('location');

        $redirectUrl = Url::fromString($authorizationResponse->headers->get('location'))->getFragment();

        parse_str($redirectUrl, $parts);

        $this->assertArrayHasKey('token_type', $parts);
        $this->assertArrayHasKey('expires_in', $parts);
        $this->assertArrayHasKey('access_token', $parts);
        $this->assertSame('Bearer', $parts['token_type']);
        $expiresInSeconds = 31622400;
        $this->assertEqualsWithDelta($expiresInSeconds, $parts['expires_in'], 5);
    }
}

class TestClient extends Client
{
    public function skipsAuthorization()
    {
        return true;
    }
}

class TestClientFactory extends ClientFactory
{
    protected $model = TestClient::class;
}
