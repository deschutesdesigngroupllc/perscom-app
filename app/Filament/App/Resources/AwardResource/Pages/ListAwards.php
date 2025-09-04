<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AwardResource\Pages;

use App\Filament\App\Resources\AwardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAwards extends ListRecords
{
    protected static string $resource = AwardResource::class;

    protected ?string $subheading = 'Awards are used to recognize personnel achievements.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
