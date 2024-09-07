<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SpecialtyResource\Pages;

use App\Filament\App\Resources\SpecialtyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpecialty extends CreateRecord
{
    protected static string $resource = SpecialtyResource::class;
}
