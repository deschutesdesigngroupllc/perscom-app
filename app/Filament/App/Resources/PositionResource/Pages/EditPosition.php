<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PositionResource\Pages;

use App\Filament\App\Resources\PositionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPosition extends EditRecord
{
    protected static string $resource = PositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
