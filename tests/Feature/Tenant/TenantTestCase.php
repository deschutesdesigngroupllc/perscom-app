<?php

namespace Tests\Feature\Tenant;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Tenant\Requests\Traits\WithTenant;
use Tests\TestCase;

class TenantTestCase extends TestCase
{
    use RefreshDatabase;
    use WithTenant;

    /**
     * @throws \Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById
     */
    protected function setUp(): void
    {
        putenv('TENANT_TESTING=true');

        parent::setUp();

        $this->setUpTenancy();
    }

    protected function tearDown(): void
    {
        $this->beforeApplicationDestroyed(function () {
            $this->tearDownTenancy();
        });

        parent::tearDown();

        putenv('TENANT_TESTING=false');
    }
}
