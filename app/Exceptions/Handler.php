<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use League\OAuth2\Server\Exception\OAuthServerException;
use Sentry\Laravel\Integration;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        TenantCouldNotBeIdentifiedOnDomainException::class,
        OAuthServerException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = ['current_password', 'password', 'password_confirmation'];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            Integration::captureUnhandledException($e);
        });
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);

        if ($request->expectsJson()) {
            return response()->json([
                'error' => [
                    'message' => $e->getMessage(),
                    'type' => class_basename($e),
                ],
            ], $response->getStatusCode());
        }

        if (! config('app.debug') && ($response->isClientError() || $response->isServerError()) && $response->getStatusCode() !== 409) {
            return match (get_class($e)) {
                TenantCouldNotBeIdentifiedOnDomainException::class => Inertia::render('Error', [
                    'status' => 404,
                    'title' => 'Organization not found.',
                    'message' => 'Sorry, we could not find the organization you’re looking for. Please check with your administrator for the proper domain.',
                    'showLink' => false,
                ])
                    ->toResponse($request)
                    ->setStatusCode(404),
                TenantAccountSetupNotComplete::class => Inertia::render('Error', [
                    'status' => 401,
                    'title' => 'Account setup not complete.',
                    'message' => 'Sorry, we are still working on setting up your account. We will email you when we are finished.',
                    'showLink' => false,
                ])
                    ->toResponse($request)
                    ->setStatusCode(401),
                default => Inertia::render('Error', [
                    'status' => $response->getStatusCode(),
                    'message' => property_exists($response, 'exception') ? $response->exception?->getMessage() : null,
                    'back' => Redirect::intended()->getTargetUrl(),
                    'showLogout' => Auth::check(),
                ])
                    ->toResponse($request)
                    ->setStatusCode($response->getStatusCode()),
            };
        }

        return $response;
    }
}
