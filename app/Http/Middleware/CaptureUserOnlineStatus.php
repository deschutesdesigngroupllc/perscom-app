<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CaptureUserOnlineStatus
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse|JsonResponse
    {
        if (Auth::check() && tenancy()->initialized) {
            $expire = now()->addMinutes(2);
            $user = Auth::user();
            if ($user) {
                Cache::tags('user.online')->put("user.online.{$user->getAuthIdentifier()}", true, $expire);
                $user->timestamps = false;
                $user->updateQuietly([
                    'last_seen_at' => now(),
                ]);
            }
        }

        return $next($request);
    }
}
