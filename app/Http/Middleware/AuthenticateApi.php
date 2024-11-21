<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApi
{
    /**
     * We cannot run the standard auth:api alongside the client credentials guard. Because we allow
     * both authorization_code and client_credential grants to access the same API resources, we run
     * both auth middleware checks here. If the request succeeds with a client_credentials token, we'll
     * flag the request so that we can check the proper scopes in our API permission service.
     *
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authenticate = app(Authenticate::class);
        $client = app(ClientCredentials::class);

        try {
            return $authenticate->handle($request, fn ($request) => $next($request), 'api');
        } catch (Exception $exception) {
            if ($exception instanceof AuthenticationException) {
                return $client->handle($request, fn ($request) => $next($request));
            }

            throw $exception;
        }
    }
}

class ClientCredentials extends CheckClientCredentials
{
    protected function validateCredentials($token): void
    {
        parent::validateCredentials($token);

        request()->attributes->add([
            'client_credentials_token' => $token,
        ]);
    }
}
