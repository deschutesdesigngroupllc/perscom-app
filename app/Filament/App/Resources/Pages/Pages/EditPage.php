<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Pages\Pages;

use App\Filament\App\Pages\Page as FilamentPage;
use App\Filament\App\Resources\Pages\PageResource;
use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->url(fn (Page $record): string => FilamentPage::getUrl(['page' => $record->slug]), shouldOpenInNewTab: true),
            DeleteAction::make(),
        ];
    }
}
