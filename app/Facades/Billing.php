<?php

declare(strict_types=1);

namespace App\Facades;

use App\Models\Tenant;
use App\Services\BillingService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool onTrial(Tenant $tenant)
 * @method static string actionUrl(Tenant $tenant, ?string $returnUrl = null)
 * @method static string checkoutUrl(Tenant $tenant, ?string $returnUrl = null)
 * @method static string portalUrl(Tenant $tenant, ?string $returnUrl = null, array $options = [])
 */
class Billing extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BillingService::class;
    }
}
