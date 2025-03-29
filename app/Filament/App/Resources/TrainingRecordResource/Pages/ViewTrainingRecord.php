<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\TrainingRecordResource\Pages;

use App\Filament\App\Resources\TrainingRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTrainingRecord extends ViewRecord
{
    protected static string $resource = TrainingRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
