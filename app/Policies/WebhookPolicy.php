<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Webhook;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class WebhookPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_webhook');
    }

    public function view(AuthUser $authUser, Webhook $webhook): bool
    {
        return $authUser->can('view_webhook');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_webhook');
    }

    public function update(AuthUser $authUser, Webhook $webhook): bool
    {
        return $authUser->can('update_webhook');
    }

    public function delete(AuthUser $authUser, Webhook $webhook): bool
    {
        return $authUser->can('delete_webhook');
    }

    public function restore(AuthUser $authUser, Webhook $webhook): bool
    {
        return $authUser->can('restore_webhook');
    }

    public function forceDelete(AuthUser $authUser, Webhook $webhook): bool
    {
        return $authUser->can('force_delete_webhook');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_webhook');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_webhook');
    }

    public function replicate(AuthUser $authUser, Webhook $webhook): bool
    {
        return $authUser->can('replicate_webhook');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_webhook');
    }
}
