<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SpecialtyResource\Pages;

use App\Filament\App\Resources\SpecialtyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpecialty extends EditRecord
{
    protected static string $resource = SpecialtyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
