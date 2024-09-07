<?php

declare(strict_types=1);

namespace App\Support\Tenancy\Bootstrappers;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class FilamentBootstrapper implements TenancyBootstrapper
{
    public function bootstrap(Tenant $tenant): void
    {
        /** @var Model|null $tenant */
        Filament::setTenant($tenant, isQuiet: true);
    }

    public function revert(): void
    {
        //
    }
}
