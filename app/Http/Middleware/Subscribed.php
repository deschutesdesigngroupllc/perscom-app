<?php

namespace App\Http\Middleware;

use App\Exceptions\SubscriptionRequired;
use Codinglabs\FeatureFlags\Facades\FeatureFlag;
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
        if ($request->isDemoMode() || $request->isCentralRequest() || FeatureFlag::isOff('billing')) {
            return $next($request);
        }

        $response =  parent::handle($request, $next, $billableType, $plan);

        if ($response->getStatusCode() === 402 && $request->expectsJson()) {
            throw new SubscriptionRequired(402, 'A subscription is required to make an API request.');
        }

        return $response;
    }
}
