<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Like;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class LikePolicy extends Policy
{
    public function before(): ?bool
    {
        if (App::isAdmin()) {
            return false;
        }

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return true;
    }

    public function view(?User $user, Like $like): bool
    {
        return Gate::check('view', $like->model);
    }

    public function create(?User $user = null): bool
    {
        return true;
    }

    public function update(?User $user, Like $like): bool
    {
        return Gate::check('update', $like->model);
    }

    public function delete(?User $user, Like $like): bool
    {
        return Gate::check('delete', $like->model);
    }

    public function restore(?User $user, Like $like): bool
    {
        return Gate::check('restore', $like->model);
    }

    public function forceDelete(?User $user, Like $like): bool
    {
        return Gate::check('forceDelete', $like->model);
    }
}
