<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CombatRecordResource\Pages;

use App\Filament\App\Resources\CombatRecordResource;
use App\Traits\Filament\InteractsWithModelNotifications;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCombatRecord extends EditRecord
{
    use InteractsWithModelNotifications;

    protected static string $resource = CombatRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
