<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AnnouncementResource\Pages;

use App\Filament\App\Resources\AnnouncementResource;
use App\Traits\Filament\ConfiguresModelNotifications;
use Filament\Resources\Pages\CreateRecord;

class CreateAnnouncement extends CreateRecord
{
    use ConfiguresModelNotifications;

    protected static string $resource = AnnouncementResource::class;
}
