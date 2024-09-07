<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class AttachmentPolicy extends Policy
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
        return optional($user)->hasRole('Admin');
    }

    public function view(?User $user, Attachment $attachment): bool
    {
        return Gate::check('view', $attachment->model);
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:attachment') || optional($user)->tokenCan('create:attachment');
    }

    public function update(?User $user, Attachment $attachment): bool
    {
        return Gate::check('update', $attachment->model);
    }

    public function delete(?User $user, Attachment $attachment): bool
    {
        return Gate::check('delete', $attachment->model);
    }

    public function restore(?User $user, Attachment $attachment): bool
    {
        return Gate::check('restore', $attachment->model);
    }

    public function forceDelete(?User $user, Attachment $attachment): bool
    {
        return Gate::check('forceDelete', $attachment->model);
    }
}
