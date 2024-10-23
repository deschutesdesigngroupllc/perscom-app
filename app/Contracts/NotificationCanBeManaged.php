<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Enums\NotificationGroup;

interface NotificationCanBeManaged
{
    public static function notificationGroup(): NotificationGroup;

    public static function notificationTitle(): string;

    public static function notificationDescription(): string;
}
