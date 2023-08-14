<?php

namespace Tests\Feature\Tenant\Repositories;

use App\Repositories\TenantRepository;
use Tests\Feature\Tenant\Models\TenantTest;

class TenantRepositoryTest extends TenantTest
{
    public function test_get_all_returns_correct_tenants()
    {
        $tenantRepository = app(TenantRepository::class);

        $tenants = $tenantRepository->getAll();

        $this->assertTrue($tenants->contains($this->tenant));
        $this->assertSame(1, $tenants->count());
    }

    public function test_find_by_key_returns_correct_tenant()
    {
        $tenantRepository = app(TenantRepository::class);

        $tenant = $tenantRepository->findByKey('id', $this->tenant->getKey());

        $this->assertNotNull($tenant);
        $this->assertSame($this->tenant->getKey(), $tenant->getKey());
    }

    public function test_find_by_id_returns_correct_tenant()
    {
        $tenantRepository = app(TenantRepository::class);

        $tenant = $tenantRepository->findById($this->tenant->getKey());

        $this->assertNotNull($tenant);
        $this->assertSame($this->tenant->getKey(), $tenant->getKey());
    }
}
