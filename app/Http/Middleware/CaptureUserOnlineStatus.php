<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Cache\TaggableStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CaptureUserOnlineStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('web')->check()) {
            optional(Auth::guard('web')->user(), function (User $user): void {
                // The online-users tag needs a taggable cache store (e.g. Redis);
                // self-hosted installs on file/database stores simply skip it.
                if (Cache::getStore() instanceof TaggableStore) {
                    Cache::tags('users_online')->put('user_online_'.$user->getAuthIdentifier(), true, now()->addMinutes(2));
                }

                $user->timestamps = false;
                $user->updateQuietly([
                    'last_seen_at' => now(),
                ]);
            });
        }

        return $next($request);
    }
}
