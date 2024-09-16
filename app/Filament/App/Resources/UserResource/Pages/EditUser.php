<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use App\Filament\App\Resources\UserResource\RelationManagers;
use App\Traits\Filament\InteractsWithFields;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    use InteractsWithFields;

    protected static string $resource = UserResource::class;

    public function getRelationManagers(): array
    {
        return [
            RelationManagers\AttachmentsRelationManager::class,
            RelationManagers\FieldsRelationManager::class,
            RelationManagers\RolesRelationManager::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
