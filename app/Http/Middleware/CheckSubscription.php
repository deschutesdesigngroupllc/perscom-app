<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Filament\Billing\Providers\Http\Middleware\VerifySparkBillableIsSubscribed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription extends VerifySparkBillableIsSubscribed
{
    private Request $request;

    public function handle($request, $next, $billableType = null, $plan = null): Response
    {
        $this->request = $request;

        if (App::isDemo() || App::isAdmin()) {
            return $next($request);
        }

        return parent::handle($request, $next, $billableType, $plan);
    }

    protected function redirect(string $billableType): string
    {
        if ($this->request->expectsJson()) {
            abort(402);
        }

        return parent::redirect($billableType);
    }
}
