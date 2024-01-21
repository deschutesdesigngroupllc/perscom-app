<?php

namespace App\Http\Middleware;

use App\Features\ApiAccessFeature;
use App\Features\OAuth2AccessFeature;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Pennant\Feature;
use Spark\Http\Middleware\VerifyBillableIsSubscribed;

class Subscribed extends VerifyBillableIsSubscribed
{
    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @param  string  $billableType
     * @param  string  $plan
     * @return Response
     */
    public function handle($request, $next, $billableType = null, $plan = null)
    {
        if (app()->environment('local') ||
            $request->isDemoMode() ||
            $request->isCentralRequest()) {
            return $next($request);
        }

        $response = parent::handle($request, $next, $billableType, $plan);
        $unsubscribed = ($response->isRedirection() && $this->redirectionIsToBillingPortal($response)) || $response->getStatusCode() === 402;

        abort_if(($unsubscribed || Feature::inactive(ApiAccessFeature::class)) && $request->routeIs('api.*'),
            402,
            'A valid subscription is required to make an API request.'
        );

        abort_if(($unsubscribed || Feature::inactive(OAuth2AccessFeature::class)) && ($request->routeIs('passport.*') || $request->routeIs('oidc.*')),
            402,
            'A valid subscription is required to use Single Sign-On (SSO).'
        );

        abort_if($unsubscribed && ! Gate::check('billing', Auth::user()),
            402,
            'The account requires a valid subscription to continue. Please contact your account administrator.'
        );

        return $response;
    }

    protected function redirectionIsToBillingPortal(mixed $response): bool
    {
        return $response instanceof RedirectResponse &&
               $response->getTargetUrl() === tenant()->url.'/'.config('spark.path').'/'.$this->guessBillableType();

    }
}
