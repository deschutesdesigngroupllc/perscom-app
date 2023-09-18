<?php

namespace Tests\Feature\Tenant\Listeners;

use Tests\Feature\Tenant\TenantTestCase;

class ConfigureApplicationForCentralContextTest extends TenantTestCase
{
    public function test_app_settings_are_reverted_to_default()
    {
        tenancy()->end();

        $this->assertEquals(config('mail.from.name'), env('MAIL_FROM_NAME'));
        $this->assertEquals(config('app.timezone'), env('UTC'));
    }
}
