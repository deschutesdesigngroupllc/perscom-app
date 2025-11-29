<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SpecialtyResource\Pages;

use App\Filament\App\Resources\SpecialtyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSpecialty extends EditRecord
{
    protected static string $resource = SpecialtyResource::class;

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
