<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Element;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class ElementPolicy extends Policy
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
        return true;
    }

    public function view(?User $user, Element $element): bool
    {
        return Gate::check('view', $element->model);
    }

    public function create(?User $user = null): bool
    {
        return true;
    }

    public function update(?User $user, Element $element): bool
    {
        return Gate::check('update', $element->model);
    }

    public function delete(?User $user, Element $element): bool
    {
        return Gate::check('delete', $element->model);
    }

    public function restore(?User $user, Element $element): bool
    {
        return Gate::check('restore', $element->model);
    }

    public function forceDelete(?User $user, Element $element): bool
    {
        return Gate::check('forceDelete', $element->model);
    }
}
