<?php

namespace Tests\Feature\Tenant\Bootstrappers;

use Spatie\Permission\PermissionRegistrar;
use Tests\Feature\Tenant\TenantTestCase;

class ConfigBootstrapperTest extends TenantTestCase
{
    public function beforeInitializingTenancy()
    {
        $this->tenant->run(function () {
            nova_set_setting_value('timezone', 'Asia/Dhaka');
        });
    }

    public function test_bootstrap_method_sets_up_config()
    {
        $this->assertEquals(config('mail.from.name'), $this->tenant->name);
        $this->assertEquals(config('app.timezone'), 'Asia/Dhaka');
        $this->assertEquals(PermissionRegistrar::$cacheKey, "'spatie.permission.cache.tenant.{$this->tenant->getTenantKey()}");
    }

    public function test_revert_method_resets_config()
    {
        tenancy()->end();

        $this->assertEquals(config('mail.from.name'), env('MAIL_FROM_NAME'));
        $this->assertEquals(config('app.timezone'), 'UTC');
        $this->assertEquals(PermissionRegistrar::$cacheKey, 'spatie.permission.cache');
    }
}
