<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Middleware\CheckSubscription;
use App\Models\User;
use App\Settings\IntegrationSettings;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;

class ApiControllerTest extends ApiTestCase
{
    public function test_api_cannot_be_reached_without_bearer_token(): void
    {
        $this->getJson(route('api.users.index', [
            'version' => config('api.version'),
        ]))->assertUnauthorized();
    }

    public function test_api_cannot_be_reached_with_invalid_version(): void
    {
        $this->withToken($this->apiKey())->getJson(route('api.users.index', [
            'version' => $this->faker->word,
        ]))->assertBadRequest();
    }

    public function test_api_cannot_be_reached_with_incorrect_perscom_id(): void
    {
        $this->withToken($this->apiKey())->getJson(route('api.users.index', [
            'version' => config('api.version'),
        ]), [
            'X-Perscom-Id' => $this->faker->randomNumber(5),
        ])->assertUnauthorized();
    }

    public function test_api_can_be_reached_with_perscom_id(): void
    {
        $this->withToken($this->apiKey())->getJson(route('api.users.index', [
            'version' => config('api.version'),
        ]))->assertSuccessful();
    }

    public function test_api_can_be_reached_with_subscription(): void
    {
        $this->withSubscription(env('STRIPE_PRODUCT_MONTH'));

        $this->withMiddleware(CheckSubscription::class);

        $this->withToken($this->apiKey())->getJson(route('api.users.index', [
            'version' => config('api.version'),
        ]))->assertSuccessful();
    }

    public function test_api_cannot_be_reached_with_incomplete_subscription(): void
    {
        $this->withSubscription(env('STRIPE_PRODUCT_MONTH'), 'incomplete');

        $this->withMiddleware(CheckSubscription::class);

        $this->withToken($this->apiKey())->getJson(route('api.users.index', [
            'version' => config('api.version'),
        ]))->assertStatus(402);
    }

    public function test_api_cannot_be_reached_with_incomplete_expired_subscription(): void
    {
        $this->withSubscription(env('STRIPE_PRODUCT_MONTH'), 'incomplete_expired');

        $this->withMiddleware(CheckSubscription::class);

        $this->withToken($this->apiKey())->getJson(route('api.users.index', [
            'version' => config('api.version'),
        ]))->assertStatus(402);
    }

    public function test_api_can_be_reached_while_on_trial(): void
    {
        $this->onTrial();

        $this->withMiddleware(CheckSubscription::class);

        $this->withToken($this->apiKey())->getJson(route('api.users.index', [
            'version' => config('api.version'),
        ]))->assertSuccessful();
    }

    public function test_api_can_be_reached_when_using_perscom_signed_jwt(): void
    {
        $token = Auth::guard('jwt')->claims([
            'scopes' => [
                'view:user',
            ],
        ])->login(User::factory()->createQuietly());

        $this->withToken($token)
            ->getJson(route('api.users.index', [
                'version' => config('api.version'),
            ]))->assertSuccessful();
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_api_can_be_reached_when_using_tenant_signed_jwt(): void
    {
        /** @var IntegrationSettings $settings */
        $settings = $this->app->make(IntegrationSettings::class);

        $tokenBuilder = (new Builder(new JoseEncoder, ChainedFormatter::default()));
        $algorithm = new Sha256;
        $signingKey = InMemory::plainText($settings->single_sign_on_key);

        $token = $tokenBuilder
            ->issuedBy(config('app.url'))
            ->relatedTo((string) User::factory()->createQuietly()->getKey())
            ->identifiedBy(Str::random(10))
            ->issuedAt(now()->toDateTimeImmutable())
            ->canOnlyBeUsedAfter(now()->toDateTimeImmutable())
            ->expiresAt(now()->addHour()->toDateTimeImmutable())
            ->withClaim('tenant', $this->tenant->getKey())
            ->withClaim('scopes', [
                'view:user',
            ])
            ->getToken($algorithm, $signingKey);

        $this->withToken($token->toString())
            ->getJson(route('api.users.index', [
                'version' => config('api.version'),
            ]))
            ->assertSuccessful();
    }

    public function test_api_cannot_be_reached_when_using_perscom_signed_jwt_without_proper_scopes(): void
    {
        $token = Auth::guard('jwt')->claims([
            'scopes' => null,
        ])->login(User::factory()->createQuietly());

        $this->withToken($token)
            ->getJson(route('api.users.index', [
                'version' => config('api.version'),
            ]))
            ->assertForbidden();
    }

    public function test_api_cannot_be_reached_when_using_tenant_signed_jwt_without_proper_scopes(): void
    {
        /** @var IntegrationSettings $settings */
        $settings = $this->app->make(IntegrationSettings::class);

        $tokenBuilder = (new Builder(new JoseEncoder, ChainedFormatter::default()));
        $algorithm = new Sha256;
        $signingKey = InMemory::plainText($settings->single_sign_on_key);

        $token = $tokenBuilder
            ->issuedBy(config('app.url'))
            ->relatedTo((string) User::factory()->createQuietly()->getKey())
            ->identifiedBy(Str::random(10))
            ->issuedAt(now()->toDateTimeImmutable())
            ->canOnlyBeUsedAfter(now()->toDateTimeImmutable())
            ->expiresAt(now()->addHour()->toDateTimeImmutable())
            ->withClaim('tenant', $this->tenant->getKey())
            ->getToken($algorithm, $signingKey);

        $this->withToken($token->toString())
            ->getJson(route('api.users.index', [
                'version' => config('api.version'),
            ]))
            ->assertForbidden();
    }
}
