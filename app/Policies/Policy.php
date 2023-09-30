<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

abstract class Policy
{
    use HandlesAuthorization;

    public function hasPermissionTo(User $user = null, $permission): Response|bool
    {
        if (Auth::getDefaultDriver() === 'api') {
            if ($client = Auth::guard('passport')->client()) { // @phpstan-ignore-line
                if ($client->type === 'client_credentials' && $token = request()->attributes->get('client_credentials_token')) {
                    return \in_array($permission, Arr::wrap($token->scopes));
                }
            }

            if (Auth::guard('jwt')->check()) {
                if ($payload = Auth::guard('jwt')->payload()) { // @phpstan-ignore-line
                    return \in_array($permission, Arr::wrap($payload->get('scope')));
                }
            }
        }

        if (Auth::getDefaultDriver() === 'web' && $user) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }
}
