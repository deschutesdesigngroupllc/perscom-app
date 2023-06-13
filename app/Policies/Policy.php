<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

abstract class Policy
{
    /**
     * @return bool
     */
    public function hasPermissionTo(User $user, $permission)
    {
        if (Auth::guard('jwt')->check()) {
            $payload = Auth::guard('jwt')->payload(); // @phpstan-ignore-line
            if (\in_array($permission, Arr::wrap($payload->get('scope')), false)) {
                return true;
            }

            //dd($permission);
        }

        if (Auth::getDefaultDriver() === 'web') {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }
}
