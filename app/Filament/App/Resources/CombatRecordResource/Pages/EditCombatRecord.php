<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CombatRecordResource\Pages;

use App\Filament\App\Resources\CombatRecordResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCombatRecord extends EditRecord
{
    protected static string $resource = CombatRecordResource::class;

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
