<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\DocumentResource\Pages;

use App\Filament\App\Resources\DocumentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;
}
