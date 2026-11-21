<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\AlertResource\Pages;

use App\Filament\Admin\Resources\AlertResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditAlert extends EditRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = AlertResource::class;

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
