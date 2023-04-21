<?php

namespace Tests\Tenant;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\WithTenant;

class TenantTestCase extends TestCase
{
    use DatabaseMigrations;
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
