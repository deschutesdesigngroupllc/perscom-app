<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\DocumentResource\Pages;

use App\Filament\App\Resources\DocumentResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('tags')
                ->color('gray')
                ->label('View Document Tags')
                ->modalContent(view('app.document-tags'))
                ->modalCancelActionLabel('Close')
                ->modalSubmitAction(false),
        ];
    }
}
