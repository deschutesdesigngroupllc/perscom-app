<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use App\Filament\App\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function getRelationManagers(): array
    {
        return [
            RelationManagers\AssignmentRecordsRelationManager::class,
            RelationManagers\AwardRecordsRelationManager::class,
            RelationManagers\CombatRecordsRelationManager::class,
            RelationManagers\QualificationRecordsRelationManager::class,
            RelationManagers\RankRecordsRelationManager::class,
            RelationManagers\ServiceRecordsRelationManager::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('email')
                ->color('gray')
                ->url(fn (?User $record) => "mailto:$record->email")
                ->openUrlInNewTab()
                ->tooltip(fn (?User $record) => $record->email),
            Actions\EditAction::make(),
        ];
    }
}
