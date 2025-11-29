<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RankResource\Pages;

use App\Filament\App\Resources\RankResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRanks extends ListRecords
{
    protected static string $resource = RankResource::class;

    protected ?string $subheading = 'Ranks provide a vertical organization hierarchy that establishes authority within an organization.';

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
