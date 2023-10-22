<?php
/*
 * Copyright (c) 2/11/23, 1:15 PM Deschutes Design Group LLC.year. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace App\Policies;

use App\Features\ApiAccessFeature;
use App\Models\PassportToken;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Laravel\Pennant\Feature;

class PassportTokenPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }

        if (Feature::inactive(ApiAccessFeature::class)) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return Gate::check('api', $user);
    }

    public function view(User $user = null, PassportToken $token): bool
    {
        return Gate::check('api', $user);
    }

    public function create(User $user = null): bool
    {
        return Gate::check('api', $user);
    }

    public function update(User $user = null, PassportToken $token): bool
    {
        return Gate::check('api', $user);
    }

    public function delete(User $user = null, PassportToken $token): bool
    {
        return Gate::check('api', $user);
    }

    public function restore(User $user = null, PassportToken $token): bool
    {
        return Gate::check('api', $user);
    }

    public function forceDelete(User $user = null, PassportToken $token): bool
    {
        return Gate::check('api', $user);
    }
}
