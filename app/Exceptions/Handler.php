<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use League\OAuth2\Server\Exception\OAuthServerException;
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
        TenantCouldNotBeIdentifiedByRequestDataException::class,
        OAuthServerException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (TenantCouldNotBeIdentifiedOnDomainException $e, $request) {
            return response()->view('errors.tenant-not-found');
        });
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Throwable                $e
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($request->routeIs('api.*') && $this->shouldReturnJson($request, $e)) {
            $response = $this->prepareJsonResponse($request, $e);
            $response->setData([
                'error' => [
                    'message' => $e->getMessage(),
                    'type' => class_basename($e)
                ]
            ]);

            if ($e instanceof AuthorizationException) {
                $response->setStatusCode(401);
            }

            return $response;
        }

        return parent::render($request, $e);
    }
}
