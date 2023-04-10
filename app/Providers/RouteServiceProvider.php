<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * The controller namespace for the application.
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
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

            Route::prefix('oauth')
                 ->as('oidc.')
                 ->middleware('web')
                 ->namespace($this->namespace)
                 ->group(base_path('routes/oidc.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
