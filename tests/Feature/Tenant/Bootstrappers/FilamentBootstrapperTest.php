<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Bootstrappers;

use Filament\Facades\Filament;
use Tests\Feature\Tenant\TenantTestCase;

class FilamentBootstrapperTest extends TenantTestCase
{
    public function test_bootstrap_method_sets_filament_tenant()
    {
        $this->assertEquals(Filament::getTenant(), $this->tenant);
    }
}
