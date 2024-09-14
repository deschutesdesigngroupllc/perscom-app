<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.Admin.{id}', function (Admin $admin, $id) {
    return $admin->id === (int) $id;
}, options: [
    'guards' => 'admin',
]);

Broadcast::channel('App.Models.User.{id}', function (User $user, $id) {
    return $user->id === (int) $id;
});
