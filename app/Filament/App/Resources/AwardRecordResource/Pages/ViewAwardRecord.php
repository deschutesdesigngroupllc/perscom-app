<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AwardRecordResource\Pages;

use App\Filament\App\Resources\AwardRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAwardRecord extends ViewRecord
{
    protected static string $resource = AwardRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
