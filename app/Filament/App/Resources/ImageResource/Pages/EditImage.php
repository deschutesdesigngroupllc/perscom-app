<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ImageResource\Pages;

use App\Filament\App\Resources\ImageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditImage extends EditRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = ImageResource::class;

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
