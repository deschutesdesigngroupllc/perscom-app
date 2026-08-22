<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Actions\Tenant;

use App\Actions\Tenant\CreateNewTenant;
use App\Models\Tenant;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Stancl\Tenancy\Events\TenantCreated as BaseTenantCreated;
use Tests\Feature\Central\CentralTestCase;

class CreateNewTenantTest extends CentralTestCase
{
    public function test_creates_a_tenant_with_a_generated_subdomain(): void
    {
        Event::fake([BaseTenantCreated::class]);
        Notification::fake();

        $action = new CreateNewTenant;

        $tenant = $action->create('Acme Organization', 'owner@example.com');

        $this->assertInstanceOf(Tenant::class, $tenant);
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->getKey(),
            'name' => 'Acme Organization',
            'email' => 'owner@example.com',
        ]);

        $this->assertCount(1, $tenant->refresh()->domains);
        $this->assertDatabaseHas('domains', [
            'tenant_id' => $tenant->getKey(),
        ]);
    }
}
