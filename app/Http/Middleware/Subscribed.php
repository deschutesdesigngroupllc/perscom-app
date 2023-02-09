<?php

namespace App\Http\Middleware;

use App\Exceptions\SubscriptionRequired;
use Codinglabs\FeatureFlags\Facades\FeatureFlag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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
        if ($request->isDemoMode() ||
            $request->isCentralRequest() ||
            FeatureFlag::isOff('billing') ||
            $request->routeIs('nova.pages.dashboard', 'nova.pages.dashboard.*', 'nova.pages.home', 'nova.api.*') ||
            Str::contains($request->path(), 'nova-vendor') ||
            Str::contains($request->path(), ['widget', 'roster'])) {
            return $next($request);
        }

        $response = parent::handle($request, $next, $billableType, $plan);
        $unsubscribed = $response->isRedirection() || $response->getStatusCode() === 402;

        if ($request->routeIs('api.*')) {
            if ($unsubscribed) {
                throw new SubscriptionRequired(402, 'A subscription is required to make an API request.');
            }

            if (! tenant()->canAccessApi()) {
                throw new SubscriptionRequired(403, 'Your plan does not include the API. Please upgrade your plan to use the API.');
            }
        }

        if ($unsubscribed && ! Auth::user()->hasPermissionTo('manage:billing')) {
            throw new SubscriptionRequired(403, 'The account requires a subscription to continue. Please contact your account administrator.');
        }

        return $response;
    }
}
