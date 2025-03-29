<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\TrainingRecordResource\Pages;

use App\Filament\App\Resources\TrainingRecordResource;
use App\Traits\Filament\InteractsWithModelNotifications;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrainingRecord extends EditRecord
{
    use InteractsWithModelNotifications;

    protected static string $resource = TrainingRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
