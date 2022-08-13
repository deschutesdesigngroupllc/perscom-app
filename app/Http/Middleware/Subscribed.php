<?php

namespace App\Http\Middleware;

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

        return parent::handle($request, $next, $billableType, $plan);
    }
}
