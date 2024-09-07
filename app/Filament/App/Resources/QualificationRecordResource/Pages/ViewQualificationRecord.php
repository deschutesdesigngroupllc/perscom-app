<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\QualificationRecordResource\Pages;

use App\Filament\App\Resources\QualificationRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewQualificationRecord extends ViewRecord
{
    protected static string $resource = QualificationRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
