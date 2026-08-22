<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Jobs\System;

use App\Actions\Tenant\SetupTenantAccount as SetupTenantAccountAction;
use App\Jobs\System\SetupTenantAccount;
use App\Models\Tenant;
use Mockery;
use Mockery\MockInterface;
use Tests\Feature\Central\CentralTestCase;

class SetupTenantAccountTest extends CentralTestCase
{
    public function test_it_delegates_to_the_setup_tenant_account_action(): void
    {
        $tenant = Tenant::factory()->createQuietly();

        $captured = null;

        $this->mock(
            SetupTenantAccountAction::class,
            function (MockInterface $mock) use (&$captured): void {
                $mock->shouldReceive('handle')
                    ->once()
                    ->with(Mockery::on(function (Tenant $argument) use (&$captured): bool {
                        $captured = $argument;

                        return true;
                    }));
            }
        );

        new SetupTenantAccount($tenant)->handle();

        $this->assertNotNull($captured);
        $this->assertSame($tenant->getKey(), $captured->getKey());
    }
}
