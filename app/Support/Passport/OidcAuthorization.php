<?php

declare(strict_types=1);

namespace App\Support\Passport;

use Illuminate\Support\Facades\Session;

class OidcAuthorization
{
    /**
     * The session key holding the time the user actively authenticated.
     */
    public const string AUTH_TIME_SESSION_KEY = 'oidc.auth_time';

    /**
     * The session key holding the nonce of an authorization request awaiting the user's consent.
     */
    public const string PENDING_SESSION_KEY = 'oidc.pending_authorization';

    /**
     * The request attribute holding the nonce of the authorization request being handled.
     */
    public const string REQUEST_ATTRIBUTE = 'oidc_authorization';

    public const string CACHE_PREFIX = 'oidc:authorization';

    /**
     * The cache key holding the nonce and authentication time bound to an issued authorization code.
     */
    public static function authCodeCacheKey(string $authCodeId): string
    {
        return sprintf('%s:auth_code:%s', static::CACHE_PREFIX, $authCodeId);
    }

    /**
     * The cache key holding the original authentication time of an access token, so it can be carried
     * over to ID tokens issued when the access token's refresh token is used.
     */
    public static function accessTokenCacheKey(string $accessTokenId): string
    {
        return sprintf('%s:access_token:%s', static::CACHE_PREFIX, $accessTokenId);
    }

    /**
     * The time the user in the current session actively authenticated, if known.
     */
    public static function authTime(): ?int
    {
        $authTime = Session::get(static::AUTH_TIME_SESSION_KEY);

        return is_int($authTime) ? $authTime : null;
    }

    /**
     * The nonce of the authorization request an authorization code is being issued for. The request is
     * either approved within the original authorization request, or approved later through the consent
     * screen, in which case the nonce was stored alongside Passport's pending authorization token.
     */
    public static function nonce(): ?string
    {
        $request = request();

        if ($request->attributes->has(static::REQUEST_ATTRIBUTE)) {
            return $request->attributes->get(static::REQUEST_ATTRIBUTE)['nonce'] ?? null;
        }

        /** @var array{auth_token?: string, nonce?: string|null}|null $pending */
        $pending = Session::get(static::PENDING_SESSION_KEY);

        if (blank($pending) || blank($request->input('auth_token')) || ($pending['auth_token'] ?? null) !== $request->input('auth_token')) {
            return null;
        }

        Session::forget(static::PENDING_SESSION_KEY);

        return $pending['nonce'] ?? null;
    }
}
