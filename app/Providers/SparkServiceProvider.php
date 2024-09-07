<?php

declare(strict_types=1);

namespace App\Providers;

use App\Features\BillingFeature;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;
use Spark\Plan;
use Spark\Spark;

use function tenant;

class SparkServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Spark::billable(Tenant::class)->resolve(function (Request $request) {
            return tenant();
        });

        Spark::billable(Tenant::class)->authorize(function (Tenant $billable, Request $request) {
            return tenant() &&
                tenant()->getTenantKey() === $billable->id &&
                Feature::active(BillingFeature::class);
        });

        Spark::billable(Tenant::class)->checkPlanEligibility(function (Tenant $billable, Plan $plan) {
            //
        });
    }
}
