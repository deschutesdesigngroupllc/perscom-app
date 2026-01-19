<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SlotResource\Pages;

use App\Filament\App\Resources\SlotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSlots extends ListRecords
{
    protected static string $resource = SlotResource::class;

    protected ?string $subheading = 'Define specific positions within units for manual roster assignments.';

    /**
     * @return CreateAction[]
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
