<?php

namespace App\Policies;

use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class ImagePolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return optional($user)->hasRole('Admin');
    }

    public function view(?User $user, Image $image): bool
    {
        return Gate::check('view', $image->model);
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:image') || optional($user)->tokenCan('create:image');
    }

    public function update(?User $user, Image $image): bool
    {
        return Gate::check('update', $image->model);
    }

    public function delete(?User $user, Image $image): bool
    {
        return Gate::check('delete', $image->model);
    }

    public function restore(?User $user, Image $image): bool
    {
        return Gate::check('restore', $image->model);
    }

    public function forceDelete(?User $user, Image $image): bool
    {
        return Gate::check('forceDelete', $image->model);
    }
}
