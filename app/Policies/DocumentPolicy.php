<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\App;

class DocumentPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:document') || optional($user)->tokenCan('view:document');
    }

    public function view(?User $user, Document $document): bool
    {
        return $this->hasPermissionTo($user, 'view:document') || optional($user)->tokenCan('view:document');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:document') || optional($user)->tokenCan('create:document');
    }

    public function update(?User $user, Document $document): bool
    {
        return $this->hasPermissionTo($user, 'update:document') || optional($user)->tokenCan('update:document');
    }

    public function delete(?User $user, Document $document): bool
    {
        return $this->hasPermissionTo($user, 'delete:document') || optional($user)->tokenCan('delete:document');
    }

    public function restore(?User $user, Document $document): bool
    {
        return $this->hasPermissionTo($user, 'delete:document') || optional($user)->tokenCan('delete:document');
    }

    public function forceDelete(?User $user, Document $document): bool
    {
        return $this->hasPermissionTo($user, 'delete:document') || optional($user)->tokenCan('delete:document');
    }
}
