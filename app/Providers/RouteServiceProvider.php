<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    protected $namespace = 'App\\Http\\Controllers';

    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::domain(config('app.api_url'))
                ->as('api.')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::domain(config('app.auth_url'))
                ->as('auth.')
                ->middleware('auth_web')
                ->namespace($this->namespace)
                ->group(base_path('routes/auth.php'));

            Route::domain(config('app.url'))
                ->as('web.')
                ->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            Route::prefix('oauth')
                ->as('passport.')
                ->namespace('Laravel\Passport\Http\Controllers')
                ->group(base_path('routes/passport.php'));

            Route::as('oidc.')
                ->namespace($this->namespace)
                ->group(base_path('routes/oidc.php'));
        });
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
