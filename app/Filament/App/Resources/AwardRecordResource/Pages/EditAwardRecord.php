<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AwardRecordResource\Pages;

use App\Filament\App\Resources\AwardRecordResource;
use App\Traits\Filament\InteractsWithModelNotifications;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAwardRecord extends EditRecord
{
    use InteractsWithModelNotifications;

    protected static string $resource = AwardRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
