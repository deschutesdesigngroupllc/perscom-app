<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Tenant;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Route::domain(config('app.url'))
            ->as('web.')
            ->middleware('web')
            ->group(base_path('routes/web.php'));
    }

    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', fn (Request $request) => App::environment('local')
            ? Limit::perMinute(2500)->by(optional($request->user())->id ?: $request->ip())
            : ($request->user()
                ? Limit::perMinute(1000)->by($request->user()->id)
                : Limit::perMinute(100)->by($request->ip())
            ));

        RateLimiter::for('register', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));

        RateLimiter::for('find-my-organization', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));

        RateLimiter::for('sms', function (Tenant|Request|null $tenant = null) {
            if (blank($tenant)) {
                return false;
            }

            if ($tenant instanceof Request) {
                return Limit::none();
            }

            return Limit::perDay(50)->by("tenant:{$tenant->getKey()}");
        });
    }
}
