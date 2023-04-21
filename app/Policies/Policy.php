<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

abstract class Policy
{
    /**
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
