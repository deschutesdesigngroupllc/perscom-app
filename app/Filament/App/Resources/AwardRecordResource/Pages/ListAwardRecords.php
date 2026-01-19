<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AwardRecordResource\Pages;

use App\Filament\App\Resources\AwardRecordResource;
use App\Filament\App\Resources\AwardRecordResource\Widgets\AwardRecordStatsOverview;
use Archilex\AdvancedTables\AdvancedTables;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAwardRecords extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = AwardRecordResource::class;

    protected ?string $subheading = 'Document awards and recognition given to personnel.';

    /**
     * @return CreateAction[]
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AwardRecordStatsOverview::class,
        ];
    }
}
