<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ServiceRecordResource\Pages;

use App\Filament\App\Resources\ServiceRecordResource;
use App\Traits\Filament\SavesAndHydratesCustomFieldData;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceRecord extends EditRecord
{
    use SavesAndHydratesCustomFieldData;

    protected static string $resource = ServiceRecordResource::class;

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
