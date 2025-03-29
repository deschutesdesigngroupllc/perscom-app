<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Issuer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IssuerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_issuer');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Issuer $issuer): bool
    {
        return $user->can('view_issuer');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_issuer');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Issuer $issuer): bool
    {
        return $user->can('update_issuer');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Issuer $issuer): bool
    {
        return $user->can('delete_issuer');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_issuer');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Issuer $issuer): bool
    {
        return $user->can('force_delete_issuer');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_issuer');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Issuer $issuer): bool
    {
        return $user->can('restore_issuer');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_issuer');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Issuer $issuer): bool
    {
        return $user->can('replicate_issuer');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_issuer');
    }
}
