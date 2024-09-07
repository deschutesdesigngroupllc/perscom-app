<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\FieldResource\Pages;

use App\Filament\App\Resources\FieldResource;
use Filament\Resources\Pages\CreateRecord;

class CreateField extends CreateRecord
{
    protected static string $resource = FieldResource::class;
}
