<?php

declare(strict_types=1);

use App\Http\Middleware\CaptureUserOnlineStatus;
use App\Http\Middleware\CheckApiVersion;
use App\Http\Middleware\CheckSubscription;
use App\Http\Middleware\CheckUserApprovalStatus;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\LogApiRequests;
use App\Http\Middleware\SentryContext;
use App\Jobs\RemoveInactiveAccounts;
use App\Jobs\ResetDemoAccount;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Sentry\Laravel\Integration;
use Spatie\ResponseCache\Middlewares\CacheResponse;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->name('api.')
                ->domain(config('api.url'))
                ->group(base_path('routes/api.php'));

            Route::as('oidc.')
                ->domain('{tenant}'.config('app.base_url'))
                ->group(base_path('routes/oidc.php'));

            Route::prefix('oauth')
                ->as('passport.')
                ->domain('{tenant}'.config('app.base_url'))
                ->namespace('Laravel\\Passport\\Http\\Controllers')
                ->group(base_path('routes/passport.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'spark/*',
        ]);

        $middleware->appendToGroup('landing', [
            HandleInertiaRequests::class,
        ]);

        $middleware->appendToGroup('api', [
            LogApiRequests::class,
            'throttle:api',
            SentryContext::class,
            CheckApiVersion::class,
            CacheResponse::class,
        ]);

        $middleware->appendToGroup('web', [
            CaptureUserOnlineStatus::class,
            CacheResponse::class,
        ]);

        $middleware->group('universal', []);

        $middleware->alias([
            'approved' => CheckUserApprovalStatus::class,
            'subscribed' => CheckSubscription::class,
        ]);

        $middleware->priority([
            HandlePrecognitiveRequests::class,
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            ValidateCsrfToken::class,
            ThrottleRequests::class,
            ThrottleRequestsWithRedis::class,
            SubstituteBindings::class,
            AuthenticatesRequests::class,
            Authorize::class,
            CheckApiVersion::class,
            SentryContext::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);

        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->routeIs('api.*') || $request->expectsJson());

        $exceptions->render(function (Exception $e, Request $request) {
            $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : null;
            $statusProperty = property_exists($e, 'status') ? $e->status : null;
            $statusUnauthenticated = $e instanceof Illuminate\Auth\AuthenticationException ? 401 : null;
            $status = $statusCode ?? $statusProperty ?? $statusUnauthenticated ?? Response::HTTP_INTERNAL_SERVER_ERROR;

            if ($request->routeIs('api.*') || $request->expectsJson()) {
                return response()->json([
                    'error' => [
                        'message' => $e->getMessage(),
                        'type' => class_basename($e),
                    ],
                ], $status);
            }
        });
    })
    ->withSchedule(function (Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->command('telescope:prune --hours=96')->dailyAt('03:00');
        $schedule->command('queue:prune-failed --hours=96')->dailyAt('04:00');
        $schedule->command('perscom:heartbeat')->environments(['staging', 'production'])->everyTenMinutes();
        $schedule->command('horizon:snapshot')->environments(['staging', 'production'])->everyFiveMinutes();
        $schedule->command('cache:prune-stale-tags')->environments(['staging', 'production'])->hourly();
        $schedule->command('perscom:prune --force --days=7')->environments(['staging', 'production'])->daily();

        $schedule->job(new ResetDemoAccount)->environments(['production'])->dailyAt('01:00');
        $schedule->job(new RemoveInactiveAccounts)->environments(['production'])->dailyAt('02:00');
    })
    ->create();
