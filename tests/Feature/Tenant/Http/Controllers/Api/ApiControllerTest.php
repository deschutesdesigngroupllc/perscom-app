<?php

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Http\Middleware\Subscribed;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Outl1ne\NovaSettings\NovaSettings;

class ApiControllerTest extends ApiTestCase
{
    public function test_api_cannot_be_reached_without_bearer_token()
    {
        $this->getJson(route('api.me.index'))
            ->assertUnauthorized();
    }

    public function test_api_cannot_be_reached_without_perscom_id()
    {
        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index'), [
            'X-Perscom-Id' => null,
        ])->assertUnauthorized();
    }

    public function test_api_cannot_be_reached_with_incorrect_perscom_id()
    {
        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index'), [
            'X-Perscom-Id' => $this->faker->randomNumber(5),
        ])->assertUnauthorized();
    }

    public function test_api_can_be_reached_with_perscom_id()
    {
        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index'))
            ->assertSuccessful();
    }

    public function test_api_cannot_be_reached_without_api_access_feature()
    {
        $this->withSubscription();

        $this->withMiddleware(Subscribed::class);

        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index'))
            ->assertStatus(402);
    }

    public function test_api_can_be_reached_with_api_access_feature()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'));

        $this->withMiddleware(Subscribed::class);

        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index'))
            ->assertSuccessful();
    }

    public function test_api_cannot_be_reached_with_incomplete_subscription()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'), 'incomplete');

        $this->withMiddleware(Subscribed::class);

        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index'))
            ->assertStatus(402);
    }

    public function test_api_cannot_be_reached_with_incomplete_expired_subscription()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'), 'incomplete_expired');

        $this->withMiddleware(Subscribed::class);

        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index'))
            ->assertStatus(402);
    }

    public function test_api_can_be_reached_while_on_trial()
    {
        $this->onTrial();

        $this->withMiddleware(Subscribed::class);

        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index'))
            ->assertSuccessful();
    }

    public function test_api_can_be_reached_while_in_demo_mode()
    {
        config()->set('demo.host', $this->domain->host);
        config()->set('demo.tenant_id', $this->tenant->getTenantKey());

        $this->withMiddleware(Subscribed::class);

        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index'))
            ->assertSuccessful();
    }

    public function test_api_can_be_reached_when_using_perscom_signed_jwt()
    {
        $token = Auth::guard('jwt')->claims([
            'scope' => [
                'view:user',
            ],
        ])->login(User::factory()->create());

        $this->withHeader('Authentication', "Bearer $token");

        $this->getJson(route('api.me.index'))
            ->assertSuccessful();
    }

    public function test_api_can_be_reached_when_using_tenant_signed_jwt()
    {
        $this->tenant->run(fn () => NovaSettings::setSettingValue('single_sign_on_key', Str::random(40)));

        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm = new Sha256();
        $signingKey = InMemory::plainText(setting('single_sign_on_key'));

        $token = $tokenBuilder
            ->issuedBy(config('app.url'))
            ->relatedTo(User::factory()->create()->getKey())
            ->identifiedBy(Str::random(10))
            ->issuedAt(now()->toDateTimeImmutable())
            ->canOnlyBeUsedAfter(now()->toDateTimeImmutable())
            ->expiresAt(now()->addHour()->toDateTimeImmutable())
            ->withClaim('scope', [
                'view:user',
            ])
            ->getToken($algorithm, $signingKey);

        $this->withToken($token->toString())
            ->getJson(route('api.me.index'))
            ->assertSuccessful();
    }

    public function test_api_cannot_be_reached_when_using_perscom_signed_jwt_without_proper_scopes()
    {
        $token = Auth::guard('jwt')->claims([
            'scope' => null,
        ])->login(User::factory()->create());

        $this->withToken($token)
            ->getJson(route('api.me.index'))
            ->assertForbidden();
    }

    public function test_api_can_be_reached_when_using_perscom_signed_jwt_and_on_basic_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_BASIC_MONTH'));

        $this->withMiddleware(Subscribed::class);

        $token = Auth::guard('jwt')->claims([
            'scope' => [
                'view:user',
            ],
        ])->login(User::factory()->create());

        $this->withToken($token)
            ->getJson(route('api.me.index'))
            ->assertSuccessful();
    }
}
