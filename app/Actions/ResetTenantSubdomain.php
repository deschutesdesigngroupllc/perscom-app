<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Tenant;
use App\Settings\DashboardSettings;
use Throwable;

class ResetTenantSubdomain
{
    /**
     * @throws Throwable
     */
    public function handle(Tenant $tenant): int
    {
        /** @var DashboardSettings $settings */
        $settings = app()->make(DashboardSettings::class);
        $settings->subdomain = null;
        $settings->save();

        return $tenant->domains()->where('is_custom_subdomain', true)->forceDelete();
    }
}
