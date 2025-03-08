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
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class IncrementMetrics
{
    public function handle(Request $request, Closure $next): Response
    {
        if (App::runningInConsole()) {
            Metric::increment(CliRequestMetric::class);
        } elseif (Str::startsWith($request->getHost(), 'api.')) {
            Metric::increment(ApiRequestMetric::class);
        } else {
            Metric::increment(HttpRequestMetric::class);
        }

        return $next($request);
    }
}
