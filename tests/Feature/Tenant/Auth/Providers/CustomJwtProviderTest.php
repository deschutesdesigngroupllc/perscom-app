<?php

namespace Tests\Feature\Tenant\Auth\Providers;

use App\Auth\Providers\CustomJwtProvider;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Tests\Feature\Tenant\TenantTestCase;

class CustomJwtProviderTest extends TenantTestCase
{
    public function test_perscom_signer_returns_correct_algorithm()
    {
        $provider = new CustomJwtProvider(
            $this->app->make('config')->get('jwt.secret'),
            $this->app->make('config')->get('jwt.algo'),
            $this->app->make('config')->get('jwt.keys')
        );

        $this->assertInstanceOf(Sha256::class, $provider->getPerscomSigner());
    }

    public function test_provider_can_successfully_decode_a_perscom_signed_token()
    {
        Auth::guard('jwt')->login($user = User::factory()->create());

        $provider = new CustomJwtProvider(
            $this->app->make('config')->get('jwt.secret'),
            $this->app->make('config')->get('jwt.algo'),
            $this->app->make('config')->get('jwt.keys')
        );

        $payload = $provider->decode(Auth::guard('jwt')->getToken());

        $this->assertIsArray($payload);
        $this->assertSame(config('app.url'), data_get($payload, 'iss'));
        $this->assertEqualsWithDelta(now()->getTimestamp(), data_get($payload, 'iat'), 5);
        $this->assertEqualsWithDelta(now()->addHour()->getTimestamp(), data_get($payload, 'exp'), 5);
        $this->assertEqualsWithDelta(now()->getTimestamp(), data_get($payload, 'nbf'), 5);
        $this->assertSame((string) $user->getKey(), data_get($payload, 'sub'));
        $this->assertSame($user->name, data_get($payload, 'name'));
        $this->assertSame($user->email, data_get($payload, 'preferred_username'));
        $this->assertSame($user->url, data_get($payload, 'profile'));
        $this->assertSame($user->email, data_get($payload, 'email'));
        $this->assertSame($user->hasVerifiedEmail(), data_get($payload, 'email_verified'));
        $this->assertSame($user->profile_photo_url, data_get($payload, 'picture'));
        $this->assertSame($this->tenant->name, data_get($payload, 'tenant_name'));
        $this->assertSame($this->tenant->getKey(), data_get($payload, 'tenant_sub'));
        $this->assertSame(config('app.locale'), data_get($payload, 'locale'));
        $this->assertSame(setting('timezone', config('app.timezone')), data_get($payload, 'zoneinfo'));
        $this->assertSame(Carbon::parse($user->updated_at)->getTimestamp(), data_get($payload, 'updated_at'));
    }

    public function test_provider_throws_an_error_when_not_token_is_invalid()
    {
        $provider = new CustomJwtProvider(
            $this->app->make('config')->get('jwt.secret'),
            $this->app->make('config')->get('jwt.algo'),
            $this->app->make('config')->get('jwt.keys')
        );

        $this->expectException(TokenInvalidException::class);

        $provider->decode(Str::random(40));
    }

    public function test_provider_throws_an_error_when_not_signed_correctly()
    {
        $this->instance(
            'tymon.jwt.provider.jwt.lcobucci',
            new CustomJwtProvider(
                Str::random(40),
                $this->app->make('config')->get('jwt.algo'),
                $this->app->make('config')->get('jwt.keys')
            )
        );

        Auth::guard('jwt')->login(User::factory()->create());

        $provider = new CustomJwtProvider(
            $this->app->make('config')->get('jwt.secret'),
            $this->app->make('config')->get('jwt.algo'),
            $this->app->make('config')->get('jwt.keys')
        );

        $this->expectException(TokenInvalidException::class);

        $provider->decode(Auth::guard('jwt')->getToken());
    }
}
