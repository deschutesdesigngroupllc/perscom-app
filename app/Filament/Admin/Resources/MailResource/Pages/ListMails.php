<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MailResource\Pages;

use App\Filament\Admin\Resources\MailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMails extends ListRecords
{
    protected static string $resource = MailResource::class;

    protected ?string $subheading = 'Send updates and notifications to all PERSCOM accounts.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
