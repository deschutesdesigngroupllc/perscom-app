<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Social;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class RedirectController
{
    public function __invoke(Request $request, string $panel, string $provider, Tenant $tenant): Response
    {
        $request->session()->put('auth.social.login.tenant', [
            'tenant' => $tenant->getTenantKey(),
            'panel' => $panel,
        ]);

        return Socialite::driver($provider)->redirect();
    }
}
