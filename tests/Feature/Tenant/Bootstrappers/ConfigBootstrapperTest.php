<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Bootstrappers;

use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Tests\Feature\Tenant\TenantTestCase;

use function tenant;

class ConfigBootstrapperTest extends TenantTestCase
{
    public function test_bootstrap_method_sets_up_config()
    {
        $this->assertEquals(config('mail.from.name'), $this->tenant->name);
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function test_revert_method_resets_config()
    {
        $tenant = tenant();
        tenancy()->end();

        $this->assertEquals(config('mail.from.name'), env('MAIL_FROM_NAME'));

        tenancy()->initialize($tenant);
    }
}
