<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\QualificationRecordResource\Pages;

use App\Filament\App\Resources\QualificationRecordResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditQualificationRecord extends EditRecord
{
    protected static string $resource = QualificationRecordResource::class;

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
