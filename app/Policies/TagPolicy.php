<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class TagPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return true;
    }

    public function view(User $user = null, Tag $tag): bool
    {
        return true;
    }

    public function create(User $user = null): bool
    {
        return true;
    }

    public function update(User $user = null, Tag $tag): bool
    {
        return true;
    }

    public function delete(User $user = null, Tag $tag): bool
    {
        return true;
    }

    public function restore(User $user = null, Tag $tag): bool
    {
        return true;
    }

    public function forceDelete(User $user = null, Tag $tag): bool
    {
        return true;
    }
}
