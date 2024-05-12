<?php

namespace Tests\Feature\Tenant\Bootstrappers;

use App\Models\Tenant;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Tests\Feature\Tenant\TenantTestCase;

class ConfigBootstrapperTest extends TenantTestCase
{
    public function beforeInitializingTenancy(Tenant $tenant): void
    {
        $tenant->run(function () {
            nova_set_setting_value('timezone', 'Asia/Dhaka');
        });
    }

    public function test_bootstrap_method_sets_up_config()
    {
        $this->assertEquals(config('mail.from.name'), $this->tenant->name);
        $this->assertEquals(config('app.timezone'), 'Asia/Dhaka');
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function test_revert_method_resets_config()
    {
        $tenant = \tenant();
        tenancy()->end();

        $this->assertEquals(config('mail.from.name'), env('MAIL_FROM_NAME'));
        $this->assertEquals(config('app.timezone'), 'UTC');

        tenancy()->initialize($tenant);
    }
}
