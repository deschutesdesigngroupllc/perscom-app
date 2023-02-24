<?php

namespace Tests\Unit\Requests;

use App\Http\Middleware\InitializeTenancyByRequestData;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Fortify\Features;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Tests\Traits\WithTenant;

class DemoRequestTest extends TestCase
{
    use WithFaker;
    use WithTenant;

    public function test_web_request_is_recognized_as_demo()
    {
        config()->set('tenancy.demo_host', $this->domain->host);
        config()->set('tenancy.demo_id', $this->tenant->getTenantKey());

        $this->get($this->tenant->url.'/login')
             ->assertSuccessful();

        $this->assertTrue(\Request::isDemoMode());
    }

    public function test_web_request_is_not_recognized_as_demo()
    {
        config()->set('tenancy.demo_host', $this->faker->domainWord.config('app.base_url'));
        config()->set('tenancy.demo_id', $this->faker->randomDigitNot($this->tenant->getTenantKey()));

        $this->get($this->tenant->url.'/login')
             ->assertSuccessful();

        $this->assertFalse(\Request::isDemoMode());
    }

    public function test_api_request_with_header_is_recognized_as_demo()
    {
        config()->set('tenancy.demo_host', $this->domain->host);
        config()->set('tenancy.demo_id', $this->tenant->getTenantKey());

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->withMiddleware(InitializeTenancyByRequestData::class);

        $this->getJson(config('app.api_url').'/'.config('app.api_version').'/me', [
            'X-Perscom-Id' => $this->tenant->getTenantKey(),
        ])->assertSuccessful();

        $this->assertTrue(\Request::isDemoMode());
    }

    public function test_api_request_with_header_is_not_recognized_as_demo()
    {
        config()->set('tenancy.demo_host', $this->faker->domainWord.config('app.base_url'));
        config()->set('tenancy.demo_id', $this->faker->randomDigitNot($this->tenant->getTenantKey()));

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->withMiddleware(InitializeTenancyByRequestData::class);

        $this->getJson(config('app.api_url').'/'.config('app.api_version').'/me', [
            'X-Perscom-Id' => $this->tenant->getTenantKey(),
        ])->assertStatus(402);

        $this->assertFalse(\Request::isDemoMode());
    }

    public function test_api_request_with_query_is_recognized_as_demo()
    {
        config()->set('tenancy.demo_host', $this->domain->host);
        config()->set('tenancy.demo_id', $this->tenant->getTenantKey());

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->withMiddleware(InitializeTenancyByRequestData::class);

        $this->getJson(config('app.api_url').'/'.config('app.api_version').'/me?perscom_id='.$this->tenant->getTenantKey())
             ->assertSuccessful();

        $this->assertTrue(\Request::isDemoMode());
    }

    public function test_api_request_with_query_is_not_recognized_as_demo()
    {
        config()->set('tenancy.demo_host', $this->faker->domainWord.config('app.base_url'));
        config()->set('tenancy.demo_id', $this->faker->randomDigitNot($this->tenant->getTenantKey()));

        Passport::actingAs($this->user, [
            'view:user',
        ]);

        $this->withMiddleware(InitializeTenancyByRequestData::class);

        $this->getJson(config('app.api_url').'/'.config('app.api_version').'/me?perscom_id='.$this->tenant->getTenantKey())
             ->assertStatus(402);

        $this->assertFalse(\Request::isDemoMode());
    }

    public function test_request_properly_configures_fortify()
    {
        config()->set('tenancy.demo_host', $this->domain->host);
        config()->set('tenancy.demo_id', $this->tenant->getTenantKey());

        $this->get($this->tenant->url.'/login')
             ->assertSuccessful();

        $this->assertSame('web', config('fortify.guard'));
        $this->assertSame('users', config('fortify.passwords'));
        $this->assertSame('', config('fortify.prefix'));
        $this->assertSame([
            Features::registration(),
            Features::resetPasswords(),
            Features::emailVerification(),
        ], config('fortify.features'));
    }
}
