<?php

namespace App\Policies;

use App\Features\OAuth2AccessFeature;
use App\Models\PassportClientLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Laravel\Pennant\Feature;

class PassportClientLogPolicy extends Policy
{
    use HandlesAuthorization;

    /**
     * @return false|void
     */
    public function before()
    {
        if (Request::isCentralRequest()) {
            return false;
        }

        if (Feature::inactive(OAuth2AccessFeature::class)) {
            return false;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return Gate::check('api', $user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PassportClientLog $log): bool
    {
        return Gate::check('api', $user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PassportClientLog $log): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PassportClientLog $log): bool
    {
        return Gate::check('api', $user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PassportClientLog $log): bool
    {
        return Gate::check('api', $user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PassportClientLog $log): bool
    {
        return Gate::check('api', $user);
    }
}
