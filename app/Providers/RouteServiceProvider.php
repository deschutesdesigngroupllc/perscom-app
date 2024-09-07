<?php

declare(strict_types=1);

namespace App\Providers;

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
        RateLimiter::for('api', function (Request $request) {
            return App::environment('local')
                ? Limit::perMinute(2500)->by(optional($request->user())->id ?: $request->ip())
                : ($request->user()
                    ? Limit::perMinute(1000)->by($request->user()->id)
                    : Limit::perMinute(100)->by($request->ip())
                );
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('find-my-organization', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
