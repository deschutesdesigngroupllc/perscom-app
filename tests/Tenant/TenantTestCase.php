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
     * @return void
     *
     * @throws \Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById
     */
    protected function setUp(): void
    {
        putenv('TENANT_TESTING=true');

        parent::setUp();

        $this->setUpTenancy();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        $this->beforeApplicationDestroyed(function () {
            $this->tearDownTenancy();
        });

        parent::tearDown();

        putenv('TENANT_TESTING=false');
    }
}
