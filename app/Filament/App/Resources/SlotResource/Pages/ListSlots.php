<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SlotResource\Pages;

use App\Filament\App\Resources\SlotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSlots extends ListRecords
{
    protected static string $resource = SlotResource::class;

    protected ?string $subheading = 'Slots provide an ordered way to define how personnel should be assigned within a unit.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
