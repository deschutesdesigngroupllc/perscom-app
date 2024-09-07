<?php

declare(strict_types=1);

namespace App\Support\Tenancy\Bootstrappers;

use Spatie\Permission\PermissionRegistrar;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class PermissionsBootstrapper implements TenancyBootstrapper
{
    public function __construct(
        protected PermissionRegistrar $registrar,
        protected ?string $originalCacheKey = null
    ) {
        $this->originalCacheKey = $this->registrar->cacheKey;
    }

    public function bootstrap(Tenant $tenant): void
    {
        $this->registrar->cacheKey = "spatie.permission.cache.tenant.{$tenant->getTenantKey()}";
    }

    public function revert(): void
    {
        $this->registrar->cacheKey = $this->originalCacheKey;
    }
}
