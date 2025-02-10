<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AssignmentRecordResource\Pages;

use App\Filament\App\Resources\AssignmentRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssignmentRecords extends ListRecords
{
    protected static string $resource = AssignmentRecordResource::class;

    protected ?string $subheading = 'Keep track of a user\'s position within the organizational hierarchy with ease.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AssignmentRecordResource\Widgets\AssignmentRecordStatsOverview::class,
        ];
    }
}
