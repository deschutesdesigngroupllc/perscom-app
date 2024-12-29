<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Metrics\ApiRequestMetric;
use App\Metrics\CliRequestMetric;
use App\Metrics\HttpRequestMetric;
use App\Metrics\Metric;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class IncrementMetrics
{
    public function handle(Request $request, Closure $next): Response
    {
        if (App::runningInConsole()) {
            Metric::increment(CliRequestMetric::class);
        } else {
            if ($request->routeIs('api.*')) {
                Metric::increment(ApiRequestMetric::class);
            } else {
                Metric::increment(HttpRequestMetric::class);
            }
        }

        return $next($request);
    }
}
