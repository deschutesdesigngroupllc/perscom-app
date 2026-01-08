<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\WebhookLog;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class WebhookLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_webhook_log');
    }

    public function view(AuthUser $authUser, WebhookLog $webhookLog): bool
    {
        return $authUser->can('view_webhook_log');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_webhook_log');
    }

    public function update(AuthUser $authUser, WebhookLog $webhookLog): bool
    {
        return $authUser->can('update_webhook_log');
    }

    public function delete(AuthUser $authUser, WebhookLog $webhookLog): bool
    {
        return $authUser->can('delete_webhook_log');
    }

    public function restore(AuthUser $authUser, WebhookLog $webhookLog): bool
    {
        return $authUser->can('restore_webhook_log');
    }

    public function forceDelete(AuthUser $authUser, WebhookLog $webhookLog): bool
    {
        return $authUser->can('force_delete_webhook_log');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_webhook_log');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_webhook_log');
    }

    public function replicate(AuthUser $authUser, WebhookLog $webhookLog): bool
    {
        return $authUser->can('replicate_webhook_log');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_webhook_log');
    }
}
