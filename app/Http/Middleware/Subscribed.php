<?php

namespace App\Http\Middleware;

use App\Features\ApiAccessFeature;
use App\Features\OAuth2AccessFeature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;
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
            $request->routeIs('nova.pages.dashboard', 'nova.pages.dashboard.*', 'nova.pages.home', 'nova.api.*') ||
            Str::contains($request->path(), 'nova-vendor') ||
            Auth::guard('jwt')->check()) {
            return $next($request);
        }

        $response = parent::handle($request, $next, $billableType, $plan);
        $unsubscribed = ($response->isRedirection() && $this->redirectionIsToBillingPortal($response)) || $response->getStatusCode() === 402;

        abort_if(($unsubscribed || Feature::inactive(ApiAccessFeature::class)) && $request->routeIs('api.*'),
            402,
            'A subscription is required to make an API request.'
        );

        abort_if(($unsubscribed || Feature::inactive(OAuth2AccessFeature::class)) && ($request->routeIs('passport.*') || $request->routeIs('oidc.*')),
            402,
            'A subscription is required to use Single Sign-On (SSO).'
        );

        abort_if($unsubscribed && ! Gate::check('billing', Auth::user()),
            402,
            'The account requires a subscription to continue. Please contact your account administrator.'
        );

        return $response;
    }

    /**
     * @return bool
     */
    protected function redirectionIsToBillingPortal($response)
    {
        return $response instanceof RedirectResponse &&
               $response->getTargetUrl() === tenant()->url.'/'.config('spark.path').'/'.$this->guessBillableType();

    }
}
