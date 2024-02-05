<?php

namespace Tests\Feature\Tenant\Models;

use App\Models\Domain;
use App\Models\Tenant;
use Tests\Feature\Tenant\TenantTestCase;

class DomainTest extends TenantTestCase
{
    public function test_url_attribute_properly_returns_url()
    {
        $domain = Domain::factory()->create([
            'domain' => $word = $this->faker->domainWord,
            'tenant_id' => Tenant::factory()->createQuietly()->getKey(),
        ]);

        $scheme = config('app.scheme');
        $base = config('app.base_url');

        $this->assertEquals("$scheme://$word$base", $domain->url);
    }

    public function test_host_attribute_properly_returns()
    {
        $domain = Domain::factory()->create([
            'domain' => $word = $this->faker->domainWord,
            'tenant_id' => Tenant::factory()->createQuietly()->getKey(),
        ]);

        $base = config('app.base_url');

        $this->assertEquals("$word$base", $domain->host);
    }
}
