<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CompetencyResource\Pages;

use App\Filament\App\Resources\CompetencyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCompetency extends CreateRecord
{
    protected static string $resource = CompetencyResource::class;
}
