<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class AttachmentPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }

        return null;
    }

    public function viewAny(User $user = null): bool
    {
        return $user?->hasRole('Admin');
    }

    public function view(User $user = null, Attachment $attachment): bool
    {
        return Gate::check('view', $attachment->model);
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:attachment') || $user?->tokenCan('create:attachment');
    }

    public function update(User $user = null, Attachment $attachment): bool
    {
        return Gate::check('update', $attachment->model);
    }

    public function delete(User $user = null, Attachment $attachment): bool
    {
        return Gate::check('delete', $attachment->model);
    }

    public function restore(User $user = null, Attachment $attachment): bool
    {
        return Gate::check('restore', $attachment->model);
    }

    public function forceDelete(User $user = null, Attachment $attachment): bool
    {
        return Gate::check('forceDelete', $attachment->model);
    }
}
