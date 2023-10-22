<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class DocumentPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:document') || $user?->tokenCan('view:document');
    }

    public function view(User $user = null, Document $document): bool
    {
        return $this->hasPermissionTo($user, 'view:document') || $user?->tokenCan('view:document');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:document') || $user?->tokenCan('create:document');
    }

    public function update(User $user = null, Document $document): bool
    {
        return $this->hasPermissionTo($user, 'update:document') || $user?->tokenCan('update:document');
    }

    public function delete(User $user = null, Document $document): bool
    {
        return $this->hasPermissionTo($user, 'delete:document') || $user?->tokenCan('delete:document');
    }

    public function restore(User $user = null, Document $document): bool
    {
        return $this->hasPermissionTo($user, 'delete:document') || $user?->tokenCan('delete:document');
    }

    public function forceDelete(User $user = null, Document $document): bool
    {
        return $this->hasPermissionTo($user, 'delete:document') || $user?->tokenCan('delete:document');
    }
}
