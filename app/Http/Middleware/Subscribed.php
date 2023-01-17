<?php

namespace App\Http\Middleware;

use App\Exceptions\SubscriptionRequired;
use Codinglabs\FeatureFlags\Facades\FeatureFlag;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Spark\Http\Middleware\VerifyBillableIsSubscribed;

class Subscribed extends VerifyBillableIsSubscribed
{
    /**
     * Verify the incoming request's user has a subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $billableType
     * @param  string  $plan
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next, $billableType = null, $plan = null)
    {
        $response = parent::handle($request, $next, $billableType, $plan);

        if ($request->isDemoMode() || $request->isCentralRequest() || FeatureFlag::isOff('billing')) {
            return $next($request);
        }

        if ($request->routeIs('nova.pages.dashboard', 'nova.pages.dashboard.*', 'nova.pages.home', 'nova.api.*')) {
            return $next($request);
        }

        if ($request->expectsJson() && $request->routeIs('api.*')) {
            if ($response->getStatusCode() === 402) {
                throw new SubscriptionRequired(402, 'A subscription is required to make an API request.');
            }

            if (!tenant()->canAccessApi()) {
                throw new SubscriptionRequired(403, 'Your plan does not include the API. Please upgrade your plan to use the API.');
            }
        }

        if ($request->expectsJson()) {
            return $next($request);
        }

        if (! Auth::user()->hasPermissionTo('manage:billing')) {
            throw new AuthorizationException('The account requires a subscription to continue. Please contact your account administrator.');
        }

        return $response;
    }
}
