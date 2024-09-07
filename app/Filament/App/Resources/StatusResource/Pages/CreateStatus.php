<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\StatusResource\Pages;

use App\Filament\App\Resources\StatusResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStatus extends CreateRecord
{
    protected static string $resource = StatusResource::class;
}
