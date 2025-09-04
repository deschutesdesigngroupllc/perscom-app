<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\StatusResource\Pages;

use App\Filament\App\Resources\StatusResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStatuses extends ListRecords
{
    protected static string $resource = StatusResource::class;

    protected ?string $subheading = 'Statuses provide a way to add additional meaning to a resource at a moment in time.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
