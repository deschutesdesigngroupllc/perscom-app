<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CaptureUserOnlineStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $expire = now()->addMinutes(2);
            $user = Auth::user();
            if ($user) {
                Cache::tags('user.online')->put("user.online.{$user->getAuthIdentifier()}", true, $expire);
                $user->update([
                    'last_seen_at' => now(),
                ]);
            }
        }
        return $next($request);
    }
}
