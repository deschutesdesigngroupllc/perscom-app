<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shield\RoleResource\Pages;

use App\Filament\Resources\Shield\RoleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected ?string $subheading = 'Add and update account roles which define access to certain account resources.';

    /**
     * @return CreateAction[]
     */
    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
