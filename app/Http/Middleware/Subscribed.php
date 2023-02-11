<?php

namespace App\Http\Middleware;

use App\Exceptions\SubscriptionRequired;
use App\Facades\Feature;
use App\Models\Enums\FeatureIdentifier;
use Codinglabs\FeatureFlags\Facades\FeatureFlag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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

        throw_if(($unsubscribed || ! Feature::isAccessible(FeatureIdentifier::FEATURE_API_ACCESS)) && $request->routeIs('api.*'),
            SubscriptionRequired::class,
            402,
            'A subscription is required to make an API request.'
        );

        throw_if(($unsubscribed || ! Feature::isAccessible(FeatureIdentifier::FEATURE_SINGLE_SIGN_ON)) && $request->routeIs('passport.*'),
            SubscriptionRequired::class,
            402,
            'Your subscription does not include use of OAuth 2.0.'
        );

        throw_if($unsubscribed && ! Gate::check('billing', Auth::user()),
            SubscriptionRequired::class,
            402,
            'The account requires a subscription to continue. Please contact your account administrator.'
        );

        return $response;
    }
}
