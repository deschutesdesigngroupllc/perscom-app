<?php

namespace App\Providers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Spark\Plan;
use Spark\Spark;

class SparkServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Spark::billable(Tenant::class)->resolve(function (Request $request) {
            return \tenant();
        });

        Spark::billable(Tenant::class)->authorize(function (
            Tenant $billable,
            Request $request
        ) {
            return \tenant() && \tenant()->getTenantKey() === $billable->id;
        });

        Spark::billable(Tenant::class)->checkPlanEligibility(function (
            Tenant $billable,
            Plan $plan
        ) {
            // if ($billable->projects > 5 && $plan->name == 'Basic') {
            //     throw ValidationException::withMessages([
            //         'plan' => 'You have too many projects for the selected plan.'
            //     ]);
            // }
        });
    }
}
