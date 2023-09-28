<?php

namespace Tests\Feature\Tenant\Listeners;

use Tests\Feature\Tenant\TenantTestCase;

class ConfigureApplicationForTenantContextTest extends TenantTestCase
{
    public function beforeInitializingTenancy()
    {
        $this->tenant->run(function () {
            nova_set_setting_value('timezone', 'Asia/Dhaka');
        });
    }

    public function test_app_settings_are_set_to_tenant_settings()
    {
        $this->assertEquals(config('mail.from.name'), $this->tenant->name);
        $this->assertEquals(config('app.timezone'), 'Asia/Dhaka');
    }
}
