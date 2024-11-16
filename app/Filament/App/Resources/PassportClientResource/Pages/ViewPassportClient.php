<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PassportClientResource\Pages;

use App\Filament\App\Resources\PassportClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPassportClient extends ViewRecord
{
    protected static string $resource = PassportClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
