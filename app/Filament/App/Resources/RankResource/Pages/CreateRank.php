<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RankResource\Pages;

use App\Filament\App\Resources\RankResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateRank extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = RankResource::class;
}
