<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'landing.app';

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
