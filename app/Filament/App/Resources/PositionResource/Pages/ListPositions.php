<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\PositionResource\Pages;

use App\Filament\App\Resources\PositionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPositions extends ListRecords
{
    protected static string $resource = PositionResource::class;

    protected ?string $subheading = 'Positions outline responsibilities assigned to certain personnel.';

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
