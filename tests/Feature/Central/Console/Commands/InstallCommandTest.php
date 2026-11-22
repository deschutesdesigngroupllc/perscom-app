<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Console\Commands;

use App\Console\Commands\InstallCommand;
use App\Models\Tenant;
use Tests\Feature\Central\CentralTestCase;

class InstallCommandTest extends CentralTestCase
{
    public function test_resetting_skips_a_tenant_whose_database_is_already_gone(): void
    {
        $tenant = Tenant::factory()->createQuietly(['tenancy_db_name' => 'perscom_missing_'.uniqid()]);

        $this->assertFalse(InstallCommand::deleteTenantDatabase($tenant));
    }

    public function test_resetting_drops_a_tenant_database_that_exists(): void
    {
        $tenant = Tenant::factory()->createQuietly(['tenancy_db_name' => 'perscom_reset_'.uniqid()]);
        $manager = $tenant->database()->manager();
        $manager->createDatabase($tenant);

        $this->assertTrue($manager->databaseExists($tenant->database()->getName()));

        $this->assertTrue(InstallCommand::deleteTenantDatabase($tenant));
        $this->assertFalse($manager->databaseExists($tenant->database()->getName()));
    }
}
