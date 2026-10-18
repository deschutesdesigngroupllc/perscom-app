<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Passport\OidcAuthorization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CaptureOidcAuthorization
{
    /**
     * Remember the nonce of an OpenID Connect authorization request so it can be bound to the
     * authorization code issued for that specific request, and later added to its ID token.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $scopes = explode(' ', (string) $request->query('scope'));

        if (! in_array('openid', $scopes, true)) {
            return $next($request);
        }

        $nonce = $request->query('nonce');

        $authorization = [
            'nonce' => is_string($nonce) && filled($nonce) ? $nonce : null,
        ];

        $request->attributes->set(OidcAuthorization::REQUEST_ATTRIBUTE, $authorization);

        $previousAuthToken = Session::get('authToken');

        $response = $next($request);

        $authToken = Session::get('authToken');

        if (filled($authToken) && $authToken !== $previousAuthToken) {
            Session::put(OidcAuthorization::PENDING_SESSION_KEY, [
                'auth_token' => $authToken,
                ...$authorization,
            ]);
        }

        return $response;
    }
}
