<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\TaskResource\Pages;

use App\Filament\App\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected ?string $subheading = 'Tasks allow administrators to assign instructions to a given group of personnel.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
