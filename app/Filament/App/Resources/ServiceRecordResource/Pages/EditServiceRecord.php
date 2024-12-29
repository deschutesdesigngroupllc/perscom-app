<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ServiceRecordResource\Pages;

use App\Filament\App\Resources\ServiceRecordResource;
use App\Traits\Filament\InteractsWithModelNotifications;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceRecord extends EditRecord
{
    use InteractsWithModelNotifications;

    protected static string $resource = ServiceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
