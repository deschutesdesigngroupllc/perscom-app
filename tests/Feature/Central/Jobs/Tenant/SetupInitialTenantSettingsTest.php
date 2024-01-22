<?php

namespace Tests\Feature\Central\Jobs\Tenant;

use App\Models\Role;
use App\Models\Tenant;
use Tests\Feature\Central\CentralTestCase;

class SetupInitialTenantSettingsTest extends CentralTestCase
{
    public function test_initial_tenant_settings_set()
    {
        $tenant = Tenant::factory()->create();
        $tenant->run(function (Tenant $tenant) {
            $this->assertEquals($tenant->name, setting('organization'));
            $this->assertEquals($tenant->email, setting('email'));
            $this->assertEquals(config('app.timezone'), setting('timezone'));
            $this->assertEquals($tenant->name, setting('dashboard_title'));
            $this->assertTrue(setting('registration_enabled'));
            $this->assertEquals(Role::query()->where('name', 'User')->pluck('id')->toArray(), setting('default_roles'));
        });
    }
}
