<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use App\Filament\App\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Conditionable;

class ViewUser extends ViewRecord
{
    use Conditionable;

    protected static string $resource = UserResource::class;

    public function getRelationManagers(): array
    {
        $relationManagers = collect();

        $hiddenFields = Arr::wrap(SettingsService::get(DashboardSettings::class, 'user_hidden_fields', []));

        $this->when(! in_array('assignment_records', $hiddenFields), fn () => $relationManagers->push(RelationManagers\AssignmentRecordsRelationManager::class));
        $this->when(! in_array('award_records', $hiddenFields), fn () => $relationManagers->push(RelationManagers\AwardRecordsRelationManager::class));
        $this->when(! in_array('combat_records', $hiddenFields), fn () => $relationManagers->push(RelationManagers\CombatRecordsRelationManager::class));
        $this->when(! in_array('qualification_records', $hiddenFields), fn () => $relationManagers->push(RelationManagers\QualificationRecordsRelationManager::class));
        $this->when(! in_array('rank_records', $hiddenFields), fn () => $relationManagers->push(RelationManagers\RankRecordsRelationManager::class));
        $this->when(! in_array('service_records', $hiddenFields), fn () => $relationManagers->push(RelationManagers\ServiceRecordsRelationManager::class));

        return $relationManagers->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('email')
                ->hidden(fn () => in_array('email', Arr::wrap(SettingsService::get(DashboardSettings::class, 'user_hidden_fields', []))))
                ->color('gray')
                ->url(fn (?User $record) => "mailto:$record->email")
                ->openUrlInNewTab()
                ->tooltip(fn (?User $record) => $record->email),
            Actions\EditAction::make(),
        ];
    }
}
