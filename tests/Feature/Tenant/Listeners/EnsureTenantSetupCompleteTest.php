<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Listeners;

use App\Exceptions\TenantAccountSetupNotComplete;
use App\Listeners\EnsureTenantSetupComplete;
use App\Models\Tenant;
use Illuminate\Support\Facades\App;
use Stancl\Tenancy\Events\TenancyInitialized;
use Stancl\Tenancy\Tenancy;
use Tests\Feature\Tenant\TenantTestCase;

class EnsureTenantSetupCompleteTest extends TenantTestCase
{
    public function test_it_does_not_throw_when_running_in_console(): void
    {
        $tenant = Tenant::factory()->makeOne(['setup_completed_at' => null]);

        $event = new TenancyInitialized($this->tenancyFor($tenant));

        new EnsureTenantSetupComplete()->handle($event);

        $this->assertFalse($tenant->setup_completed);
    }

    public function test_it_throws_when_tenant_setup_is_not_complete(): void
    {
        $app = App::partialMock();
        $app->shouldReceive('runningInConsole')->andReturnFalse();
        $app->shouldReceive('runningConsoleCommand')->andReturnFalse();

        $tenant = Tenant::factory()->makeOne(['setup_completed_at' => null]);

        $event = new TenancyInitialized($this->tenancyFor($tenant));

        $this->expectException(TenantAccountSetupNotComplete::class);

        new EnsureTenantSetupComplete()->handle($event);
    }

    public function test_it_does_not_throw_when_tenant_setup_is_complete(): void
    {
        $app = App::partialMock();
        $app->shouldReceive('runningInConsole')->andReturnFalse();
        $app->shouldReceive('runningConsoleCommand')->andReturnFalse();

        $tenant = Tenant::factory()->makeOne(['setup_completed_at' => now()]);

        $event = new TenancyInitialized($this->tenancyFor($tenant));

        new EnsureTenantSetupComplete()->handle($event);

        $this->assertTrue($tenant->setup_completed);
    }

    private function tenancyFor(Tenant $tenant): Tenancy
    {
        $tenancy = new Tenancy;
        $tenancy->tenant = $tenant;

        return $tenancy;
    }
}
