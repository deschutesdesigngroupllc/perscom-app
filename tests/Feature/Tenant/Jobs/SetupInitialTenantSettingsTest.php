<?php

namespace Tests\Feature\Tenant\Jobs;

use App\Models\Role;
use Tests\Feature\Tenant\TenantTestCase;

class SetupInitialTenantSettingsTest extends TenantTestCase
{
    public function test_initial_tenant_settings_set()
    {
        $this->assertEquals($this->tenant->name, setting('organization'));
        $this->assertEquals($this->tenant->email, setting('email'));
        $this->assertEquals(config('app.timezone'), setting('timezone'));
        $this->assertEquals($this->tenant->name, setting('dashboard_title'));
        $this->assertTrue(setting('registration_enabled'));
        $this->assertEquals(Role::query()->where('name', 'User')->pluck('id')->toArray(), setting('default_roles'));
    }
}
