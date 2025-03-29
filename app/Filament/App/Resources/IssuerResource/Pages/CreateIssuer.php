<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\IssuerResource\Pages;

use App\Filament\App\Resources\IssuerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIssuer extends CreateRecord
{
    protected static string $resource = IssuerResource::class;
}
