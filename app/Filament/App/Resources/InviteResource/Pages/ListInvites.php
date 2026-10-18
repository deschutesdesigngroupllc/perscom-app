<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\InviteResource\Pages;

use App\Filament\Actions\InviteAction;
use App\Filament\App\Resources\InviteResource;
use Filament\Resources\Pages\ListRecords;

class ListInvites extends ListRecords
{
    protected static string $resource = InviteResource::class;

    protected ?string $subheading = 'Manage invitations sent to personnel to create or claim their profile.';

    /**
     * @return InviteAction[]
     */
    protected function getHeaderActions(): array
    {
        return [
            InviteAction::make(),
        ];
    }
}
