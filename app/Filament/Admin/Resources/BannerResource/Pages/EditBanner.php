<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BannerResource\Pages;

use App\Filament\Admin\Resources\BannerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditBanner extends EditRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = BannerResource::class;

    /**
     * @return DeleteAction[]
     */
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
