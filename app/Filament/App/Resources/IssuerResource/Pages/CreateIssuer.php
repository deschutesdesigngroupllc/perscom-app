<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\IssuerResource\Pages;

use App\Filament\App\Resources\IssuerResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateIssuer extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = IssuerResource::class;
}
