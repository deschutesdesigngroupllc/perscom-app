<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Support\JwtAuth\Providers;

use App\Models\User;
use App\Settings\IntegrationSettings;
use App\Support\JwtAuth\Providers\CustomJwtProvider;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Parser;
use Tests\Feature\Tenant\TenantTestCase;

class CustomJwtProviderTest extends TenantTestCase
{
    /**
     * @throws BindingResolutionException
     */
    public function test_it_will_validate_a_good_perscom_signed_token(): void
    {
        $user = User::factory()->createQuietly();

        /** @var CustomJwtProvider $provider */
        $provider = $this->app->make(CustomJwtProvider::class);

        /** @var string $token */
        $token = Auth::guard('jwt')->login($user);

        $parser = new Parser(new JoseEncoder);
        $token = $parser->parse($token);

        $this->assertTrue($provider->getConfig()->validator()->validate($token, ...$provider->getConfig()->validationConstraints()));
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_it_will_validate_a_good_tenant_signed_token(): void
    {
        $user = User::factory()->createQuietly();

        /** @var CustomJwtProvider $provider */
        $provider = $this->app->make(CustomJwtProvider::class);

        /** @var IntegrationSettings $settings */
        $settings = $this->app->make(IntegrationSettings::class);

        $config = Configuration::forSymmetricSigner(
            new Sha256,
            InMemory::plainText($settings->single_sign_on_key),
        );

        $token = $config->builder()
            ->issuedAt(CarbonImmutable::now())
            ->canOnlyBeUsedAfter(CarbonImmutable::now())
            ->expiresAt(now()->addHour()->toDateTimeImmutable())
            ->relatedTo((string) $user->getKey())
            ->withClaim('scopes', ['*'])
            ->getToken($config->signer(), $config->signingKey());

        $this->assertTrue($provider->getConfig()->validator()->validate($token, ...$provider->getConfig()->validationConstraints()));
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_it_will_not_validate_an_incorrect_perscom_signed_key(): void
    {
        $user = User::factory()->createQuietly();

        /** @var CustomJwtProvider $provider */
        $provider = $this->app->make(CustomJwtProvider::class, [
            'secret' => Str::password(symbols: false),
        ]);

        /** @var string $token */
        $token = Auth::guard('jwt')->login($user);

        $parser = new Parser(new JoseEncoder);
        $token = $parser->parse($token);

        $this->assertFalse($provider->getConfig()->validator()->validate($token, ...$provider->getConfig()->validationConstraints()));
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_it_will_not_validate_an_incorrect_tenant_signed_key(): void
    {
        $user = User::factory()->createQuietly();

        /** @var CustomJwtProvider $provider */
        $provider = $this->app->make(CustomJwtProvider::class);

        $config = Configuration::forSymmetricSigner(
            new Sha256,
            InMemory::plainText(Str::password(symbols: false)),
        );

        $token = $config->builder()
            ->issuedAt(CarbonImmutable::now())
            ->canOnlyBeUsedAfter(CarbonImmutable::now())
            ->expiresAt(now()->addHour()->toDateTimeImmutable())
            ->relatedTo((string) $user->getKey())
            ->withClaim('scopes', ['*'])
            ->getToken($config->signer(), $config->signingKey());

        $this->assertFalse($provider->getConfig()->validator()->validate($token, ...$provider->getConfig()->validationConstraints()));
    }
}
