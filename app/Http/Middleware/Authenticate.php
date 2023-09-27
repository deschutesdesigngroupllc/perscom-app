<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Contracts\Container\BindingResolutionException;

class Authenticate extends Middleware
{
    /**
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $this->authenticate($request, $guards);
        } catch (AuthenticationException) {
            $clientCredentialMiddleware = app()->make(CheckClientCredentials::class);

            return $clientCredentialMiddleware->handle($request, $next);
        }

        return $next($request);
    }

    /**
     * @return string|void|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
