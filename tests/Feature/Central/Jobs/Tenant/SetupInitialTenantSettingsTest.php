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
            $settings = nova_get_settings();

            $this->assertEquals($tenant->name, data_get($settings, 'organization'));
            $this->assertEquals($tenant->email, data_get($settings, 'email'));
            $this->assertEquals(config('app.timezone'), data_get($settings, 'timezone'));
            $this->assertEquals($tenant->name, data_get($settings, 'dashboard_title'));
            $this->assertTrue(data_get($settings, 'registration_enabled'));
            $this->assertEquals(Role::query()->where('name', 'User')->pluck('id')->toArray(), data_get($settings, 'default_roles'));
        });
    }
}
