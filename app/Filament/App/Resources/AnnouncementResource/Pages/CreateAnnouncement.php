<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AnnouncementResource\Pages;

use App\Filament\App\Resources\AnnouncementResource;
use App\Traits\Filament\ConfiguresModelNotifications;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateAnnouncement extends CreateRecord
{
    use ConfiguresModelNotifications;

    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = AnnouncementResource::class;
}
