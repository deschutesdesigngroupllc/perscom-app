<?php

namespace App\Policies;

use App\Models\Like;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class LikePolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return true;
    }

    public function view(User $user = null, Like $like): bool
    {
        return Gate::check('view', $like->model);
    }

    public function create(User $user = null): bool
    {
        return true;
    }

    public function update(User $user = null, Like $like): bool
    {
        return Gate::check('update', $like->model);
    }

    public function delete(User $user = null, Like $like): bool
    {
        return Gate::check('delete', $like->model);
    }

    public function restore(User $user = null, Like $like): bool
    {
        return Gate::check('restore', $like->model);
    }

    public function forceDelete(User $user = null, Like $like): bool
    {
        return Gate::check('forceDelete', $like->model);
    }
}
