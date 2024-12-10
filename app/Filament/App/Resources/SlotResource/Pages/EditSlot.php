<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SlotResource\Pages;

use App\Filament\App\Resources\SlotResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSlot extends EditRecord
{
    protected static string $resource = SlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
