<?php

namespace Tests\Feature\Tenant\Models;

use Tests\Feature\Tenant\TenantTestCase;

class TenantTest extends TenantTestCase
{
    public function test_custom_domain_attribute_is_returned()
    {
        $this->tenant->domains()->create([
            'domain' => 'foo',
            'is_custom_subdomain' => true,
        ]);
        $this->tenant->load('domains');

        $this->assertNotNull($this->tenant->custom_domain);
        $this->assertEquals('foo', $this->tenant->custom_domain->domain);
    }

    public function test_fallback_domain_attribute_is_returned()
    {
        $this->assertNotNull($this->tenant->fallback_domain);
        $this->assertEquals($this->tenant->domain->domain, $this->tenant->fallback_domain->domain);
    }

    public function test_domain_attribute_returns_custom_domain()
    {
        $this->tenant->domains()->create([
            'domain' => 'foo',
            'is_custom_subdomain' => true,
        ]);
        $this->tenant->load('domains');

        $this->assertEquals($this->tenant->custom_domain, $this->tenant->domain);
    }

    public function test_fallback_url_attribute_returns_proper_url()
    {
        $scheme = config('app.scheme');
        $base = config('app.base_url');

        $this->assertEquals("$scheme://{$this->tenant->fallback_domain->domain}$base", $this->tenant->fallback_url);
    }

    public function test_custom_url_attribute_returns_proper_url()
    {
        $this->tenant->domains()->create([
            'domain' => 'foo',
            'is_custom_subdomain' => true,
        ]);
        $this->tenant->load('domains');

        $scheme = config('app.scheme');
        $base = config('app.base_url');

        $this->assertEquals("$scheme://{$this->tenant->custom_domain->domain}$base", $this->tenant->custom_url);
    }

    public function test_url_attribute_properly_returns_domain()
    {
        $scheme = config('app.scheme');
        $base = config('app.base_url');

        $this->assertEquals("$scheme://{$this->tenant->domain->domain}$base", $this->tenant->url);
    }

    public function test_url_attribute_properly_returns_custom_domain()
    {
        $this->tenant->domains()->create([
            'domain' => 'foo',
            'is_custom_subdomain' => true,
        ]);
        $this->tenant->load('domains');

        $scheme = config('app.scheme');
        $base = config('app.base_url');

        $this->assertEquals("$scheme://{$this->tenant->domain->domain}$base", $this->tenant->url);
    }
}
