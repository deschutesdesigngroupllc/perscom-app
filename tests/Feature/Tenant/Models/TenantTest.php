<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Models;

use App\Models\Tenant;
use Tests\Feature\Tenant\TenantTestCase;

class TenantTest extends TenantTestCase
{
    public function test_custom_domain_attribute_is_returned(): void
    {
        $tenant = Tenant::factory()->createQuietly();
        $tenant->domains()->createQuietly([
            'domain' => $domain = $this->faker->unique()->word,
            'is_custom_subdomain' => true,
        ]);

        $this->assertNotNull($tenant->custom_domain);
        $this->assertEquals($domain, $tenant->custom_domain->domain);
    }

    public function test_fallback_domain_attribute_is_returned(): void
    {
        $tenant = Tenant::factory()->createQuietly();
        $tenant->domains()->createQuietly([
            'domain' => $this->faker->unique()->word,
        ]);

        $this->assertNotNull($tenant->fallback_domain);
        $this->assertEquals($tenant->domain->domain, $tenant->fallback_domain->domain);
    }

    public function test_domain_attribute_returns_custom_domain(): void
    {
        $tenant = Tenant::factory()->createQuietly();
        $tenant->domains()->createQuietly([
            'domain' => $this->faker->unique()->word,
            'is_custom_subdomain' => true,
        ]);

        $this->assertEquals($tenant->custom_domain, $tenant->domain);
    }

    public function test_fallback_url_attribute_returns_proper_url(): void
    {
        $tenant = Tenant::factory()->createQuietly();
        $tenant->domains()->createQuietly([
            'domain' => $this->faker->unique()->word,
        ]);

        $scheme = config('app.scheme');
        $base = config('app.base_url');

        $this->assertEquals(sprintf('%s://%s%s', $scheme, $tenant->fallback_domain->domain, $base), $tenant->fallback_url);
    }

    public function test_custom_url_attribute_returns_proper_url(): void
    {
        $tenant = Tenant::factory()->createQuietly();
        $tenant->domains()->createQuietly([
            'domain' => $this->faker->unique()->word,
            'is_custom_subdomain' => true,
        ]);

        $scheme = config('app.scheme');
        $base = config('app.base_url');

        $this->assertEquals(sprintf('%s://%s%s', $scheme, $tenant->custom_domain->domain, $base), $tenant->custom_url);
    }

    public function test_url_attribute_properly_returns_domain(): void
    {
        $tenant = Tenant::factory()->createQuietly();
        $tenant->domains()->createQuietly([
            'domain' => $this->faker->unique()->word,
        ]);

        $scheme = config('app.scheme');
        $base = config('app.base_url');

        $this->assertEquals(sprintf('%s://%s%s', $scheme, $tenant->domain->domain, $base), $tenant->url);
    }

    public function test_url_attribute_properly_returns_custom_domain(): void
    {
        $tenant = Tenant::factory()->createQuietly();
        $tenant->domains()->createQuietly([
            'domain' => $this->faker->unique()->word,
            'is_custom_subdomain' => true,
        ]);

        $scheme = config('app.scheme');
        $base = config('app.base_url');

        $this->assertEquals(sprintf('%s://%s%s', $scheme, $tenant->domain->domain, $base), $tenant->url);
    }

    public function test_route_does_not_add_a_tenant_query_parameter(): void
    {
        $this->assertSame($this->tenant->url.'/oauth/authorize', $this->tenant->route('passport.authorizations.authorize'));
        $this->assertSame($this->tenant->url.'/oauth/token?foo=bar', $this->tenant->route('passport.token', ['foo' => 'bar']));
    }

    public function test_route_fills_the_tenant_parameter_when_the_route_has_one(): void
    {
        $url = $this->tenant->route('auth.social.redirect', ['panel' => 'app', 'provider' => 'discord']);

        $this->assertStringStartsWith($this->tenant->url.'/app/oauth/discord/', $url);
        $this->assertStringNotContainsString('?tenant=', $url);
    }
}
