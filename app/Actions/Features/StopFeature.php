<?php

declare(strict_types=1);

namespace App\Actions\Features;

use App\Models\Feature;
use App\Models\Tenant;

class StopFeature
{
    public static function handle(Tenant $tenant, Feature $feature)
    {
        return rescue(function () use ($tenant, $feature) {
            return $tenant->subscription()
                ->removePrice($feature->price_id);
        }, false);
    }
}
