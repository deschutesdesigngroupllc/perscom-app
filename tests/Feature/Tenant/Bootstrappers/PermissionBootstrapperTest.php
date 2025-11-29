<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Bootstrappers;

use Spatie\Permission\PermissionRegistrar;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Tests\Feature\Tenant\TenantTestCase;

use function tenant;

class PermissionBootstrapperTest extends TenantTestCase
{
    public function test_bootstrap_method_sets_permission_cache_key(): void
    {
        /** @var PermissionRegistrar $registrar */
        $registrar = $this->app->make(PermissionRegistrar::class);

        $this->assertEquals($registrar->cacheKey, 'spatie.permission.cache.tenant.'.$this->tenant->getTenantKey());
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function test_revert_method_resets_permission_cache_key(): void
    {
        $tenant = tenant();
        tenancy()->end();

        /** @var PermissionRegistrar $registrar */
        $registrar = $this->app->make(PermissionRegistrar::class);

        $this->assertNotEquals($registrar->cacheKey, 'spatie.permission.cache.tenant.'.$this->tenant->getTenantKey());

        tenancy()->initialize($tenant);
    }
}
