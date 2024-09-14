<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Exception;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use function response;

class RedirectSocialProvider
{
    /**
     * @throws Exception
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('socialite.filament.app.oauth.redirect')) {
            return response()->redirectToRoute('auth.social.redirect', [
                'panel' => Filament::getPanel()->getId(),
                'provider' => $request->route('provider'),
                'tenant' => tenant('slug'),
            ]);
        }

        return $next($request);
    }
}
