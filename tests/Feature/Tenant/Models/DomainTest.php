<?php

namespace Tests\Feature\Tenant\Models;

use App\Models\Domain;
use Tests\Feature\Tenant\TenantTestCase;

class DomainTest extends TenantTestCase
{
    public function test_url_attribute_properly_returns_url()
    {
        $domain = Domain::factory()->create([
            'domain' => 'foo',
            'tenant_id' => $this->tenant->getKey(),
        ]);

        $scheme = config('app.scheme');
        $base = config('app.base_url');

        $this->assertEquals("$scheme://foo$base", $domain->url);
    }

    public function test_host_attribute_properly_returns()
    {
        $domain = Domain::factory()->create([
            'domain' => 'foo',
            'tenant_id' => $this->tenant->getKey(),
        ]);

        $base = config('app.base_url');

        $this->assertEquals("foo$base", $domain->host);
    }
}
