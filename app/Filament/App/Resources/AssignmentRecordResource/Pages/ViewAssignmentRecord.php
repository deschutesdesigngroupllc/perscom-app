<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AssignmentRecordResource\Pages;

use App\Filament\App\Resources\AssignmentRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAssignmentRecord extends ViewRecord
{
    protected static string $resource = AssignmentRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
