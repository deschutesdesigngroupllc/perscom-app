<?php

namespace Tests\Feature\Tenant;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Tenant\Requests\Traits\WithTenant;
use Tests\TestCase;

class TenantTestCase extends TestCase
{
    use RefreshDatabase;
    use WithTenant;

    /**
     * @var Admin
     */
    protected $superAdmin = null;

    /**
     * @throws \Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById
     */
    protected function setUp(): void
    {
        putenv('TENANT_TESTING=true');

        parent::setUp();

        $this->superAdmin = Admin::factory()->create();

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
