<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

use function in_array;

abstract class Policy
{
    use HandlesAuthorization;

    public function hasPermissionTo(?User $user, string $permission): bool
    {
        if (Auth::getDefaultDriver() === 'api') {
            if ($client = Auth::guard('passport')->client()) { // @phpstan-ignore-line
                if ($client->type === 'client_credentials' && $token = request()->attributes->get('client_credentials_token')) {
                    $scopes = Arr::wrap($token->scopes);

                    return in_array('*', $scopes) || in_array($permission, Arr::wrap($token->scopes));
                }
            }

            if (Auth::guard('jwt')->check()) {
                if ($payload = Auth::guard('jwt')->payload()) { // @phpstan-ignore-line
                    $scopes = Arr::wrap($payload->get('scopes'));

                    return in_array('*', $scopes) || in_array($permission, $scopes);
                }
            }
        }

        if (Auth::getDefaultDriver() === 'web' && $user) {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }
}
