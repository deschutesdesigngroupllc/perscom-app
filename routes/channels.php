<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.Admin.{id}', fn (Admin $admin, $id): bool => $admin->id === (int) $id, options: [
    'guards' => 'admin',
]);

Broadcast::channel('App.Models.User.{id}', fn (User $user, $id): bool => $user->id === (int) $id);
