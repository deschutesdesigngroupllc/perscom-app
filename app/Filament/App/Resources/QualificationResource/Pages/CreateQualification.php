<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\QualificationResource\Pages;

use App\Filament\App\Resources\QualificationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQualification extends CreateRecord
{
    protected static string $resource = QualificationResource::class;
}
