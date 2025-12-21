<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\FormResource\Pages;

use App\Filament\App\Resources\FormResource;
use App\Traits\Filament\ConfiguresModelNotifications;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditForm extends EditRecord
{
    use ConfiguresModelNotifications;

    protected static string $resource = FormResource::class;

    /**
     * @return DeleteAction[]
     */
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
