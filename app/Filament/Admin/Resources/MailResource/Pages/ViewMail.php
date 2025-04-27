<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MailResource\Pages;

use App\Filament\Admin\Resources\MailResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewMail extends ViewRecord
{
    protected static string $resource = MailResource::class;

    public function getHeading(): string|Htmlable
    {
        return $this->record->subject ?? 'View Email';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
