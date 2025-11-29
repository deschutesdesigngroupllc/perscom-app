<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Bootstrappers;

use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Tests\Feature\Tenant\TenantTestCase;

use function tenant;

class ConfigBootstrapperTest extends TenantTestCase
{
    public function test_bootstrap_method_sets_up_config(): void
    {
        $this->assertEquals(config('mail.from.name'), 'PERSCOM - '.$this->tenant->name);
        $this->assertEquals(config('responsecache.cache_tag'), 'tenant'.$this->tenant->getKey());
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function test_revert_method_resets_config(): void
    {
        $tenant = tenant();
        tenancy()->end();

        $this->assertEquals(config('mail.from.name'), env('MAIL_FROM_NAME'));
        $this->assertEquals(config('responsecache.cache_tag'), '');

        tenancy()->initialize($tenant);
    }
}
