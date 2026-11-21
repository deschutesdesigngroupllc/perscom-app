<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\AdminResource\Pages;

use App\Filament\Admin\Resources\AdminResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateAdmin extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = AdminResource::class;
}
