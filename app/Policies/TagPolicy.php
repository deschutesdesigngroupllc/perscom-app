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

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return true;
    }

    public function view(?User $user, Tag $tag): bool
    {
        return true;
    }

    public function create(?User $user = null): bool
    {
        return true;
    }

    public function update(?User $user, Tag $tag): bool
    {
        return true;
    }

    public function delete(?User $user, Tag $tag): bool
    {
        return true;
    }

    public function restore(?User $user, Tag $tag): bool
    {
        return true;
    }

    public function forceDelete(?User $user, Tag $tag): bool
    {
        return true;
    }
}
