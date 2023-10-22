<?php

namespace App\Providers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spark\Plan;
use Spark\Spark;

class SparkServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Spark::ignoreMigrations();
    }

    public function boot(): void
    {
        Spark::billable(Tenant::class)->resolve(function (Request $request) {
            return \tenant();
        });

        Spark::billable(Tenant::class)->authorize(function (Tenant $billable, Request $request) {
            return \tenant() &&
                   \tenant()->getTenantKey() === $billable->id &&
                   ! $request->isDemoMode() &&
                   ! $request->isCentralRequest() &&
                   Gate::check('billing', $request->user());
        });

        Spark::billable(Tenant::class)->checkPlanEligibility(function (Tenant $billable, Plan $plan) {
        });
    }
}
