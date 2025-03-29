<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CompetencyResource\Pages;

use App\Filament\App\Resources\CompetencyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompetency extends EditRecord
{
    protected static string $resource = CompetencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
