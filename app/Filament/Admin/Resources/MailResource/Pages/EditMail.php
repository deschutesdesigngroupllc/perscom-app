<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MailResource\Pages;

use App\Filament\Admin\Resources\MailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMail extends EditRecord
{
    protected static string $resource = MailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
