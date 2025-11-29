<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BannerResource\Pages;

use App\Filament\Admin\Resources\BannerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBanners extends ListRecords
{
    protected static string $resource = BannerResource::class;

    protected ?string $subheading = 'Manage banners to display important messages or announcements.';

    /**
     * @return CreateAction[]
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
