<?php

declare(strict_types=1);

namespace App\Support\Tenancy\Bootstrappers;

use Filament\Facades\Filament;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class FilamentBootstrapper implements TenancyBootstrapper
{
    public function bootstrap(Tenant $tenant): void
    {
        /** @var Tenant|null $tenant */
        /** @phpstan-ignore-next-line varTag.nativeType */
        Filament::setTenant($tenant, isQuiet: true);
    }

    public function revert(): void
    {
        //
    }
}
