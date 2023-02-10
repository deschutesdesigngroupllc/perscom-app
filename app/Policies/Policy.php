<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Policy
{
    /**
     * @param  User  $user
     * @param    $permission
     * @return bool
     */
    public function hasPermissionTo(User $user, $permission)
    {
        if (Auth::getDefaultDriver() === 'web') {
            return $user->hasPermissionTo($permission, 'web');
        }

        return false;
    }
}
