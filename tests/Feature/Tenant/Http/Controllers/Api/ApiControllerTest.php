<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Http\Controllers\Api;

use App\Features\ApiAccessFeature;
use App\Http\Middleware\CheckSubscription;
use App\Models\User;
use App\Settings\IntegrationSettings;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Laravel\Pennant\Feature;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;

class ApiControllerTest extends ApiTestCase
{
    public function test_api_cannot_be_reached_without_bearer_token()
    {
        $this->getJson(route('api.me.index', [
            'version' => config('api.version'),
        ]))->assertUnauthorized();
    }

    public function test_api_cannot_be_reached_without_perscom_id()
    {
        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index', [
            'version' => config('api.version'),
        ]), [
            'X-Perscom-Id' => null,
        ])->assertUnauthorized();
    }

    public function test_api_cannot_be_reached_with_incorrect_perscom_id()
    {
        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index', [
            'version' => config('api.version'),
        ]), [
            'X-Perscom-Id' => $this->faker->randomNumber(5),
        ])->assertUnauthorized();
    }

    public function test_api_can_be_reached_with_perscom_id()
    {
        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index', [
            'version' => config('api.version'),
        ]))->assertSuccessful();
    }

    public function test_api_cannot_be_reached_without_api_access_feature()
    {
        $this->withSubscription();

        $this->withMiddleware(CheckSubscription::class);

        Feature::define(ApiAccessFeature::class, false);

        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index', [
            'version' => config('api.version'),
        ]))->assertStatus(402);
    }

    public function test_api_can_be_reached_with_api_access_feature()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'));

        $this->withMiddleware(CheckSubscription::class);

        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index', [
            'version' => config('api.version'),
        ]))->assertSuccessful();
    }

    public function test_api_cannot_be_reached_with_incomplete_subscription()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'), 'incomplete');

        $this->withMiddleware(CheckSubscription::class);

        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index', [
            'version' => config('api.version'),
        ]))->assertStatus(402);
    }

    public function test_api_cannot_be_reached_with_incomplete_expired_subscription()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_PRO_MONTH'), 'incomplete_expired');

        $this->withMiddleware(CheckSubscription::class);

        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index', [
            'version' => config('api.version'),
        ]))->assertStatus(402);
    }

    public function test_api_can_be_reached_while_on_trial()
    {
        $this->onTrial();

        $this->withMiddleware(CheckSubscription::class);

        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index', [
            'version' => config('api.version'),
        ]))->assertSuccessful();
    }

    public function test_api_can_be_reached_while_in_demo_mode()
    {
        // TODO: Fix test
        $this->markTestSkipped();

        config(['app.env' => 'demo']);

        $this->withMiddleware(CheckSubscription::class);

        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index', [
            'version' => config('api.version'),
        ]))->assertSuccessful();

        config(['app.env' => 'testing']);
    }

    public function test_api_can_be_reached_when_using_perscom_signed_jwt()
    {
        $token = Auth::guard('jwt')->claims([
            'scopes' => [
                'view:user',
            ],
        ])->login(User::factory()->create());

        $this->withToken($token)
            ->getJson(route('api.me.index', [
                'version' => config('api.version'),
            ]))->assertSuccessful();
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_api_can_be_reached_when_using_tenant_signed_jwt()
    {
        /** @var IntegrationSettings $settings */
        $settings = $this->app->make(IntegrationSettings::class);

        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm = new Sha256();
        $signingKey = InMemory::plainText($settings->single_sign_on_key);

        $token = $tokenBuilder
            ->issuedBy(config('app.url'))
            ->relatedTo((string) User::factory()->create()->getKey())
            ->identifiedBy(Str::random(10))
            ->issuedAt(now()->toDateTimeImmutable())
            ->canOnlyBeUsedAfter(now()->toDateTimeImmutable())
            ->expiresAt(now()->addHour()->toDateTimeImmutable())
            ->withClaim('scopes', [
                'view:user',
            ])
            ->getToken($algorithm, $signingKey);

        $this->withToken($token->toString())
            ->getJson(route('api.me.index', [
                'version' => config('api.version'),
            ]))
            ->assertSuccessful();
    }

    public function test_api_cannot_be_reached_when_using_perscom_signed_jwt_without_proper_scopes()
    {
        $token = Auth::guard('jwt')->claims([
            'scopes' => null,
        ])->login(User::factory()->create());

        $this->withToken($token)
            ->getJson(route('api.me.index', [
                'version' => config('api.version'),
            ]))
            ->assertForbidden();
    }

    public function test_api_cannot_be_reached_when_using_perscom_signed_jwt_and_on_basic_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_BASIC_MONTH'));

        $this->withMiddleware(CheckSubscription::class);

        $token = Auth::guard('jwt')->claims([
            'scopes' => [
                'view:user',
            ],
        ])->login(User::factory()->create());

        $this->withToken($token)
            ->getJson(route('api.me.index', [
                'version' => config('api.version'),
            ]))
            ->assertPaymentRequired();
    }

    public function test_api_cannot_be_reached_when_using_tenant_signed_jwt_and_on_basic_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_BASIC_MONTH'));

        $this->withMiddleware(CheckSubscription::class);

        /** @var IntegrationSettings $settings */
        $settings = $this->app->make(IntegrationSettings::class);

        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm = new Sha256();
        $signingKey = InMemory::plainText($settings->single_sign_on_key);

        $token = $tokenBuilder
            ->issuedBy(config('app.url'))
            ->relatedTo((string) User::factory()->create()->getKey())
            ->identifiedBy(Str::random(10))
            ->issuedAt(now()->toDateTimeImmutable())
            ->canOnlyBeUsedAfter(now()->toDateTimeImmutable())
            ->expiresAt(now()->addHour()->toDateTimeImmutable())
            ->withClaim('scopes', [
                'view:user',
            ])
            ->getToken($algorithm, $signingKey);

        $this->withToken($token->toString())
            ->getJson(route('api.me.index', [
                'version' => config('api.version'),
            ]))
            ->assertPaymentRequired();
    }

    public function test_api_cannot_be_reached_when_using_api_key_and_on_basic_plan()
    {
        $this->withSubscription(env('STRIPE_PRODUCT_BASIC_MONTH'));

        $this->withMiddleware(CheckSubscription::class);

        Passport::actingAs(User::factory()->create(), [
            'view:user',
        ]);

        $this->getJson(route('api.me.index', [
            'version' => config('api.version'),
        ]))->assertPaymentRequired();
    }
}
