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
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }

        if (Feature::inactive(OAuth2AccessFeature::class)) {
            return false;
        }

        return null;
    }

    public function viewAny(User $user = null): bool
    {
        return Gate::check('api', $user);
    }

    public function view(User $user = null, Client $client): bool
    {
        if ($client->name === 'Default Personal Access Client' || $client->name === 'Default Password Grant Client') {
            return false;
        }

        return Gate::check('api', $user);
    }

    public function create(User $user = null): bool
    {
        return Gate::check('api', $user);
    }

    public function update(User $user = null, Client $client): bool
    {
        if ($client->name === 'Default Personal Access Client' || $client->name === 'Default Password Grant Client') {
            return false;
        }

        return Gate::check('api', $user);
    }

    public function delete(User $user = null, Client $client): bool
    {
        if ($client->name === 'Default Personal Access Client' || $client->name === 'Default Password Grant Client') {
            return false;
        }

        return Gate::check('api', $user);
    }

    public function restore(User $user = null, Client $client): bool
    {
        if ($client->name === 'Default Personal Access Client' || $client->name === 'Default Password Grant Client') {
            return false;
        }

        return Gate::check('api', $user);
    }

    public function forceDelete(User $user = null, Client $client): bool
    {
        if ($client->name === 'Default Personal Access Client' || $client->name === 'Default Password Grant Client') {
            return false;
        }

        return Gate::check('api', $user);
    }
}
