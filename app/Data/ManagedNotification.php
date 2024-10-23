<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Enums\NotificationGroup;
use Spatie\LaravelData\Data;

class ManagedNotification extends Data
{
    public NotificationGroup $group;

    public string $title;

    public string $description;

    public string $notificationClass;
}
