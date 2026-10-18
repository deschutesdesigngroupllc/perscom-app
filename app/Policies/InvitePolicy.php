<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Invite;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class InvitePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_invite');
    }

    public function view(AuthUser $authUser, Invite $invite): bool
    {
        return $authUser->can('view_invite');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_invite');
    }

    public function update(AuthUser $authUser, Invite $invite): bool
    {
        return $authUser->can('update_invite');
    }

    public function delete(AuthUser $authUser, Invite $invite): bool
    {
        return $authUser->can('delete_invite');
    }
}
