<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Passport\IdTokenResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CaptureOidcAuthorization
{
    /**
     * Remember the nonce and authentication time of an OpenID Connect authorization request
     * so they can be added to the ID token issued when the authorization code is exchanged.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(config('passport.guard'));
        $scopes = explode(' ', (string) $request->query('scope'));

        if ($user !== null && filled($request->query('client_id')) && in_array('openid', $scopes, true)) {
            Cache::put(
                IdTokenResponse::authorizationCacheKey((string) $request->query('client_id'), (string) $user->getAuthIdentifier()),
                [
                    'nonce' => $request->query('nonce'),
                    'auth_time' => now()->getTimestamp(),
                ],
                now()->addMinutes(10)
            );
        }

        return $next($request);
    }
}
