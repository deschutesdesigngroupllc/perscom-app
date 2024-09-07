<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PositionResource\Pages;

use App\Filament\App\Resources\PositionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePosition extends CreateRecord
{
    protected static string $resource = PositionResource::class;
}
