<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use App\Filament\App\Resources\UserResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\FieldsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Traits\Filament\BuildsCustomFieldComponents;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    use BuildsCustomFieldComponents;

    protected static string $resource = UserResource::class;

    public function getRelationManagers(): array
    {
        return [
            AttachmentsRelationManager::class,
            FieldsRelationManager::class,
            RolesRelationManager::class,
        ];
    }

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
