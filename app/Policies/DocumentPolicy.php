<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Document;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class DocumentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_document');
    }

    public function view(AuthUser $authUser, Document $document): bool
    {
        return $authUser->can('view_document');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_document');
    }

    public function update(AuthUser $authUser, Document $document): bool
    {
        return $authUser->can('update_document');
    }

    public function delete(AuthUser $authUser, Document $document): bool
    {
        return $authUser->can('delete_document');
    }

    public function restore(AuthUser $authUser, Document $document): bool
    {
        return $authUser->can('restore_document');
    }

    public function forceDelete(AuthUser $authUser, Document $document): bool
    {
        return $authUser->can('force_delete_document');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_document');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_document');
    }

    public function replicate(AuthUser $authUser, Document $document): bool
    {
        return $authUser->can('replicate_document');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_document');
    }
}
