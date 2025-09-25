<?php

declare(strict_types=1);

use App\Http\Middleware\ApiHeaders;
use App\Http\Middleware\AttachTraceAndRequestId;
use App\Http\Middleware\AuthenticateApi;
use App\Http\Middleware\CaptureUserOnlineStatus;
use App\Http\Middleware\CheckApiVersion;
use App\Http\Middleware\CheckSubscription;
use App\Http\Middleware\CheckUserApprovalStatus;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\IncrementMetrics;
use App\Http\Middleware\InitializeTenancyBySubdomain;
use App\Http\Middleware\LogApiRequest;
use App\Http\Middleware\LogApiResponse;
use App\Http\Middleware\MoveApiKeyQueryParameterToHeader;
use App\Http\Middleware\SentryContext;
use App\Jobs\RemoveInactiveAccounts;
use App\Jobs\ResetDemoAccount;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Passport\Http\Middleware\CheckForAnyScope;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;
use League\OAuth2\Server\Exception\OAuthServerException;
use Sentry\Laravel\Integration;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand;
use Symfony\Component\HttpFoundation\Response;
use Torchlight\Middleware\RenderTorchlight;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('api')
                ->name('api.')
                ->domain(config('api.url'))
                ->group(base_path('routes/api.php'));

            Route::domain(config('app.auth_url'))
                ->as('auth.')
                ->middleware('web')
                ->group(base_path('routes/auth.php'));

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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('filament.app.auth.login'));

        $middleware->validateCsrfTokens(except: [
            'spark/*',
        ]);

        $middleware->appendToGroup('landing', [
            SentryContext::class,
            HandleInertiaRequests::class,
            'cache.headers:public;max_age=2592000;etag',
        ]);

        $middleware->appendToGroup('api', [
            'throttle:api',
        ]);

        $middleware->appendToGroup('web', [
            CaptureUserOnlineStatus::class,
        ]);

        $middleware->group('universal', []);

        $middleware->alias([
            'auth_api' => AuthenticateApi::class,
            'approved' => CheckUserApprovalStatus::class,
            'cache.headers' => SetCacheHeaders::class,
            'feature' => EnsureFeaturesAreActive::class,
            'scope' => CheckForAnyScope::class,
            'subscribed' => CheckSubscription::class,
        ]);

        $middleware->append([
            IncrementMetrics::class,
            AttachTraceAndRequestId::class,
            CheckForMaintenanceMode::class,
            RenderTorchlight::class,
        ]);

        /**
         * When middleware is executing in the order of priority, the responses are executed
         * in reverse order. For example, during the request, the middleware at the top is
         * executed first. When returning the response, the middleware at the top is executed last.
         */
        $middleware->priority([
            LogApiResponse::class,
            AttachTraceAndRequestId::class,
            ApiHeaders::class,
            SentryContext::class,
            LogApiRequest::class,
            HandlePrecognitiveRequests::class,
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            ValidateCsrfToken::class,
            ThrottleRequests::class,
            ThrottleRequestsWithRedis::class,
            SubstituteBindings::class,
            MoveApiKeyQueryParameterToHeader::class,
            CheckSubscription::class,
            AuthenticateApi::class,
            AuthenticatesRequests::class,
            Authorize::class,
            CheckApiVersion::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);

        $exceptions->context(fn (): array => [
            'requestId' => context()->get('request_id'),
            'traceId' => context()->get('trace_id'),
        ]);

        $exceptions->shouldRenderJsonWhen(fn (Request $request): bool => $request->routeIs('api.*') || $request->expectsJson());

        $exceptions->dontReport([
            OAuthServerException::class,
        ]);

        $exceptions->render(function (Exception $e, Request $request) {
            $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : null;
            $statusProperty = property_exists($e, 'status') ? $e->status : null;
            $statusUnauthenticated = $e instanceof Illuminate\Auth\AuthenticationException ? 401 : null;
            $status = $statusCode ?? $statusProperty ?? $statusUnauthenticated ?? Response::HTTP_INTERNAL_SERVER_ERROR;

            if ($request->routeIs('api.*') || $request->expectsJson()) {
                $response = [
                    'error' => [
                        'message' => match (true) {
                            $status === Response::HTTP_UNAUTHORIZED => 'You are not authenticated. Please provide a valid API key that contains your PERSCOM ID to continue.',
                            $status === Response::HTTP_PAYMENT_REQUIRED => 'A valid subscription is required to complete this request.',
                            $status === Response::HTTP_FORBIDDEN => 'The API key provided does not have the correct permissions and/or scopes to perform the requested action.',
                            $status === Response::HTTP_NOT_FOUND => 'The requested resource or endpoint could not be found.',
                            $status === Response::HTTP_INTERNAL_SERVER_ERROR => 'There was a server error with your last request. Please try again.',
                            $status === Response::HTTP_TOO_MANY_REQUESTS => 'You have exceeded the API rate limit. Please wait a minute before trying again.',
                            $status === Response::HTTP_SERVICE_UNAVAILABLE => 'The API is currently down for maintenance. Please check back later.',
                            default => $e->getMessage(),
                        },
                        'type' => class_basename($e),
                        'request_id' => Context::get('request_id'),
                        'trace_id' => Context::get('trace_id'),
                    ],
                ];

                if (config('app.debug', false)) {
                    $response['error']['file'] = $e->getFile();
                    $response['error']['line'] = $e->getLine();
                    $response['error']['route'] = [
                        'name' => Route::currentRouteName(),
                        'action' => Route::currentRouteAction(),
                    ];
                }

                if ($e instanceof ValidationException) {
                    $response['error']['errors'] = $e->errors();
                }

                if ($request->routeIs('api.widgets.*')) {
                    return response()->view('widgets.error', [
                        'status' => $statusCode,
                        'message' => data_get($response, 'error.message') ?? 'Unknown error.',
                    ]);
                }

                return response()->json($response, $status);
            }
        });
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('cache:prune-stale-tags')->hourly();
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
        $schedule->command('queue:prune-failed --hours=96')->dailyAt('06:00'); // 11 PM
        $schedule->command('queue:prune-batches --hours=96')->dailyAt('06:15'); // 11:15 PM
        $schedule->command('telescope:prune --hours=96')->dailyAt('06:30'); // 11:30 PM
        $schedule->command('perscom:prune --force')->environments(['staging', 'production'])->dailyAt('07:00'); // 12 AM
        $schedule->command('perscom:optimize --force')->environments(['staging', 'production'])->weeklyOn(Schedule::SATURDAY);
        $schedule->command('perscom:backup-clean')->environments('production')->dailyAt('08:00'); // 1 AM
        $schedule->command('perscom:backup')->environments('production')->dailyAt('09:00'); // 2 AM
        $schedule->command('perscom:calculate-schedules')->environments('production')->dailyAt('10:00'); // 3 AM
        $schedule->command('perscom:event-notifications')->environments('production')->dailyAt('10:00'); // 3 AM
        $schedule->command('perscom:recurring-messages')->environments('production')->dailyAt('10:00'); // 3 AM

        $schedule->command(RunHealthChecksCommand::class)->environments('production')->everyMinute();
        $schedule->command(DispatchQueueCheckJobsCommand::class)->environments('production')->everyMinute();
        $schedule->command(ScheduleCheckHeartbeatCommand::class)->environments('production')->everyMinute();

        $schedule->job(new ResetDemoAccount)->environments('demo')->dailyAt('13:00'); // 6 AM
        $schedule->job(new RemoveInactiveAccounts)->environments('production')->dailyAt('12:00'); // 5 AM
    })
    ->withBroadcasting(__DIR__.'/../routes/channels.php', [
        'middleware' => [
            'web', InitializeTenancyBySubdomain::class, 'universal',
        ],
    ])
    ->create();
