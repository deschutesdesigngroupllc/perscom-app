<?php

namespace App\Policies;

use App\Features\OAuth2AccessFeature;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;
use Laravel\Passport\Client;
use Laravel\Pennant\Feature;

class PassportClientPolicy extends Policy
{
    use HandlesAuthorization;

    /**
     * @return bool
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
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $this->hasPermissionTo($user, 'manage:api') || $user->tokenCan('manage:api');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \Laravel\Passport\Client  $client
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Client $client)
    {
        if ($client->name === 'Default Personal Access Client' || $client->name === 'Default Password Grant Client') {
            return false;
        }

        return $this->hasPermissionTo($user, 'manage:api') || $user->tokenCan('manage:api');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $this->hasPermissionTo($user, 'manage:api') || $user->tokenCan('manage:api');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \Laravel\Passport\Client  $client
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Client $client)
    {
        if ($client->name === 'Default Personal Access Client' || $client->name === 'Default Password Grant Client') {
            return false;
        }

        return $this->hasPermissionTo($user, 'manage:api') || $user->tokenCan('manage:api');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \Laravel\Passport\Client  $client
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Client $client)
    {
        if ($client->name === 'Default Personal Access Client' || $client->name === 'Default Password Grant Client') {
            return false;
        }

        return $this->hasPermissionTo($user, 'manage:api') || $user->tokenCan('manage:api');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \Laravel\Passport\Client  $client
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Client $client)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \Laravel\Passport\Client  $client
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Client $client)
    {
        //
    }
}
