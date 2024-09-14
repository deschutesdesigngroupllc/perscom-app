<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Social;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Spatie\Url\Url;
use Symfony\Component\HttpFoundation\Response;

class CallbackController
{
    public function __invoke(Request $request, $provider): Response
    {
        $session = $request->session()->get('auth.social.login.tenant');

        $tenant = Tenant::findOrFail(data_get($session, 'tenant'));

        $url = Url::fromString($tenant->url)
            ->withPath("app/oauth/callback/$provider")
            ->withQueryParameters($request->query->all())
            ->__toString();

        return redirect($url);
    }
}
