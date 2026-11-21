<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\GroupResource\Pages;

use App\Filament\App\Resources\GroupResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateGroup extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = GroupResource::class;
}
