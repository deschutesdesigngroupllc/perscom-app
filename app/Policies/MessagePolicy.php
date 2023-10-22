<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class MessagePolicy extends Policy
{
    public function before(): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function viewAny(User $user = null): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function view(User $user = null, Message $message): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function create(User $user = null): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function update(User $user = null, Message $message): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function delete(User $user = null, Message $message): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function restore(User $user = null, Message $message): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }

    public function forceDelete(User $user = null, Message $message): bool
    {
        if (Request::isCentralRequest()) {
            return true;
        }

        return false;
    }
}
