<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Facades\Billing;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $authenticated = array_any(['web', 'api'], fn (string $guard) => Auth::guard($guard)->check());

        if (App::isDemo() ||
            App::isAdmin() ||
            ! $authenticated ||
            ! config('tenancy.enabled') ||
            blank(config('cashier.secret'))) {
            return $next($request);
        }

        $tenant = tenant();

        if ($tenant && (Billing::onTrial($tenant) || $tenant->subscribed())) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(Response::HTTP_PAYMENT_REQUIRED);
        }

        return redirect()->to($tenant ? Billing::actionUrl($tenant, url()->current()) : '/');
    }
}
