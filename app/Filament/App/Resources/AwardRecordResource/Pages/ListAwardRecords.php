<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AwardRecordResource\Pages;

use App\Filament\App\Resources\AwardRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAwardRecords extends ListRecords
{
    protected static string $resource = AwardRecordResource::class;

    protected ?string $subheading = 'Keep track of a users meritorious achievements and acknowledgements.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AwardRecordResource\Widgets\AwardRecordStatsOverview::class,
        ];
    }
}
