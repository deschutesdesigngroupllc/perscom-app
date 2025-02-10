<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ServiceRecordResource\Pages;

use App\Filament\App\Resources\ServiceRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServiceRecords extends ListRecords
{
    protected static string $resource = ServiceRecordResource::class;

    protected ?string $subheading = 'Maintain general records that don’t fit into any specific category.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ServiceRecordResource\Widgets\ServiceRecordStatsOverview::class,
        ];
    }
}
