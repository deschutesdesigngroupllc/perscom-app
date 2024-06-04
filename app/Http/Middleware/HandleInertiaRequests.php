<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Middleware;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaRequests extends Middleware
{
    /**
     * @var string
     */
    protected $rootView = 'app';

    /**
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        $response = parent::handle($request, $next);
        $location = $response->headers->get('Location');

        if (\is_string($location) && $response->isRedirection() && $request->inertia()) {
            $host = parse_url($location, PHP_URL_HOST);
            if (! Str::endsWith($host, config('tenancy.central_domains'))) {
                return Inertia::location($location);
            }
        }

        return $response;
    }

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'tenant' => tenant('name'),
            'tenant_id' => tenant('id'),
            'flash' => [
                'banner' => fn () => $request->session()->get('banner'),
                'status' => fn () => $request->session()->get('status'),
            ],
        ]);
    }
}
