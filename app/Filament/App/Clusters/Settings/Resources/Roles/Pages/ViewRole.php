<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Resources\Roles\Pages;

use App\Filament\App\Clusters\Settings\Resources\Roles\RoleResource;
use App\Models\Role;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    /**
     * @return EditAction[]
     */
    protected function getActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn (Role $record): bool => RoleResource::canEdit($record)),
            DeleteAction::make()
                ->visible(fn (Role $record): bool => RoleResource::canDelete($record)),
        ];
    }
}
