<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Resources\Roles\Pages;

use App\Filament\App\Clusters\Settings\Resources\Roles\RoleResource;
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
