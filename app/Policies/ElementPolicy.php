<?php

namespace App\Policies;

use App\Models\Element;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class ElementPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }

        return null;
    }

    public function viewAny(User $user = null): bool
    {
        return true;
    }

    public function view(User $user = null, Element $element): bool
    {
        return Gate::check('view', $element->model);
    }

    public function create(User $user = null): bool
    {
        return true;
    }

    public function update(User $user = null, Element $element): bool
    {
        return Gate::check('update', $element->model);
    }

    public function delete(User $user = null, Element $element): bool
    {
        return Gate::check('delete', $element->model);
    }

    public function restore(User $user = null, Element $element): bool
    {
        return Gate::check('restore', $element->model);
    }

    public function forceDelete(User $user = null, Element $element): bool
    {
        return Gate::check('forceDelete', $element->model);
    }
}
