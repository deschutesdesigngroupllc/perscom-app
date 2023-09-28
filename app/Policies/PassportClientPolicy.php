<?php

namespace App\Policies;

use App\Features\OAuth2AccessFeature;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Laravel\Passport\Client;
use Laravel\Pennant\Feature;

class PassportClientPolicy extends Policy
{
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
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user = null)
    {
        return Gate::check('api', $user);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user = null, Client $client)
    {
        if ($client->name === 'Default Personal Access Client' || $client->name === 'Default Password Grant Client') {
            return false;
        }

        return Gate::check('api', $user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return Gate::check('api', $user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, Client $client)
    {
        if ($client->name === 'Default Personal Access Client' || $client->name === 'Default Password Grant Client') {
            return false;
        }

        return Gate::check('api', $user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, Client $client)
    {
        if ($client->name === 'Default Personal Access Client' || $client->name === 'Default Password Grant Client') {
            return false;
        }

        return Gate::check('api', $user);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, Client $client)
    {
        if ($client->name === 'Default Personal Access Client' || $client->name === 'Default Password Grant Client') {
            return false;
        }

        return Gate::check('api', $user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, Client $client)
    {
        if ($client->name === 'Default Personal Access Client' || $client->name === 'Default Password Grant Client') {
            return false;
        }

        return Gate::check('api', $user);
    }
}
