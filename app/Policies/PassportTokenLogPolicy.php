<?php

namespace App\Policies;

use App\Features\ApiAccessFeature;
use App\Models\PassportTokenLog;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;
use Laravel\Pennant\Feature;

class PassportTokenLogPolicy extends Policy
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

    public function view(User $user = null, PassportTokenLog $log): bool
    {
        return Gate::check('api', $user);
    }

    public function create(User $user = null): bool
    {
        return false;
    }

    public function update(User $user = null, PassportTokenLog $log): bool
    {
        return false;
    }

    public function delete(User $user = null, PassportTokenLog $log): bool
    {
        return Gate::check('api', $user);
    }

    public function restore(User $user = null, PassportTokenLog $log): bool
    {
        return Gate::check('api', $user);
    }

    public function forceDelete(User $user = null, PassportTokenLog $log): bool
    {
        return Gate::check('api', $user);
    }
}
