<?php

namespace App\Policies\Passport;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;
use Laravel\Passport\Client;

class ClientPolicy
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

        if (! tenant()->canAccessApi()) {
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
        return $user->hasPermissionTo('manage:api', 'web') || $user->tokenCan('manage:api');
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

        return $user->hasPermissionTo('manage:api', 'web') || $user->tokenCan('manage:api');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('manage:api', 'web') || $user->tokenCan('manage:api');
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

        return $user->hasPermissionTo('manage:api', 'web') || $user->tokenCan('manage:api');
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

        return $user->hasPermissionTo('manage:api', 'web') || $user->tokenCan('manage:api');
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
