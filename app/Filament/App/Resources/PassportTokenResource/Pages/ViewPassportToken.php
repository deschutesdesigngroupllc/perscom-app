<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PassportTokenResource\Pages;

use App\Filament\App\Resources\PassportTokenResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPassportToken extends ViewRecord
{
    protected static string $resource = PassportTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
