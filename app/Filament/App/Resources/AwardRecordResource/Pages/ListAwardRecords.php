<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AwardRecordResource\Pages;

use App\Filament\App\Resources\AwardRecordResource;
use Archilex\AdvancedTables\AdvancedTables;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAwardRecords extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = AwardRecordResource::class;

    protected ?string $subheading = 'Keep track of a user\'s meritorious achievements and acknowledgments effortlessly.';

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
