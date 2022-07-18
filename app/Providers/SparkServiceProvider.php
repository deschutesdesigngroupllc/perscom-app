<?php

namespace App\Providers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Spark\Plan;
use Spark\Spark;

class SparkServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Spark::ignoreMigrations();
    }

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

        Spark::billable(Tenant::class)->authorize(function (Tenant $billable, Request $request) {
            return \tenant() &&
                \tenant()->getTenantKey() === $billable->id &&
                $request->user()->hasPermissionTo('manage:billing');
        });

        Spark::billable(Tenant::class)->checkPlanEligibility(function (Tenant $billable, Plan $plan) {
            // if ($billable->projects > 5 && $plan->name == 'Basic') {
            //     throw ValidationException::withMessages([
            //         'plan' => 'You have too many projects for the selected plan.'
            //     ]);
            // }
        });
    }
}
