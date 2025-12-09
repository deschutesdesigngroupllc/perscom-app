<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Pages\Pages;

use App\Filament\App\Resources\Pages\PageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected ?string $subheading = 'Pages provide a way to create custom web pages within your dashboard using the Twig Template Engine.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
