<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\TrainingRecordResource\Pages;

use App\Filament\App\Resources\TrainingRecordResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTrainingRecord extends EditRecord
{
    protected static string $resource = TrainingRecordResource::class;

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
