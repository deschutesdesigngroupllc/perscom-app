<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\NewsfeedResource\Pages;

use App\Filament\App\Resources\NewsfeedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNewsfeeds extends ListRecords
{
    protected static string $resource = NewsfeedResource::class;

    protected ?string $subheading = 'Keep your users up-to-date with the latest news happening in your organization.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
