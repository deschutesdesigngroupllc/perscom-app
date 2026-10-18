<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Support\Passport\OidcAuthorization;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RecordOidcAuthenticationTime
{
    /**
     * Record when the user actively authenticated so ID tokens can report an accurate auth_time.
     * Logins restored from a "remember me" cookie are not an active authentication, and the time
     * of the original authentication is unknown, so the authentication time is cleared instead.
     */
    public function handle(Login $event): void
    {
        $guardName = config('passport.guard', config('auth.defaults.guard'));

        if ($event->guard !== $guardName) {
            return;
        }

        $guard = Auth::guard($event->guard);

        if ($guard instanceof SessionGuard && $guard->viaRemember()) {
            Session::forget(OidcAuthorization::AUTH_TIME_SESSION_KEY);

            return;
        }

        Session::put(OidcAuthorization::AUTH_TIME_SESSION_KEY, now()->getTimestamp());
    }
}
