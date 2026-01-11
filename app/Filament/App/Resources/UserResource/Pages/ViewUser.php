<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use App\Filament\App\Resources\UserResource\RelationManagers\AssignmentRecordsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\AwardRecordsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\CombatRecordsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\QualificationRecordsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\RankRecordsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\ServiceRecordsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\TrainingRecordsRelationManager;
use App\Models\User;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Components\ViewComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;

class ViewUser extends ViewRecord
{
    use Conditionable;

    protected static string $resource = UserResource::class;

    public function getRelationManagers(): array
    {
        $relationManagers = collect();

        $hiddenFields = Arr::wrap(SettingsService::get(DashboardSettings::class, 'user_hidden_fields', []));

        $this->when(! in_array('assignment_records', $hiddenFields), fn () => $relationManagers->push(AssignmentRecordsRelationManager::class));
        $this->when(! in_array('award_records', $hiddenFields), fn () => $relationManagers->push(AwardRecordsRelationManager::class));
        $this->when(! in_array('combat_records', $hiddenFields), fn () => $relationManagers->push(CombatRecordsRelationManager::class));
        $this->when(! in_array('qualification_records', $hiddenFields), fn () => $relationManagers->push(QualificationRecordsRelationManager::class));
        $this->when(! in_array('rank_records', $hiddenFields), fn () => $relationManagers->push(RankRecordsRelationManager::class));
        $this->when(! in_array('service_records', $hiddenFields), fn () => $relationManagers->push(ServiceRecordsRelationManager::class));
        $this->when(! in_array('training_records', $hiddenFields), fn () => $relationManagers->push(TrainingRecordsRelationManager::class));

        return $relationManagers->toArray();
    }

    public function getSubheading(): string|Htmlable|null
    {
        return new Stringable()
            /** @phpstan-ignore-next-line property.notFound */
            ->when($this->getRecord()->rank, fn (Stringable $str) => $str->append(' ')->append($this->getRecord()->rank->abbreviation)->append(' ')->append($this->getRecord()->rank->name))
            /** @phpstan-ignore-next-line property.notFound */
            ->when($this->getRecord()->position, fn (Stringable $str) => $str->append(', ')->append($this->getRecord()->position->name))
            /** @phpstan-ignore-next-line property.notFound */
            ->when($this->getRecord()->unit, fn (Stringable $str) => $str->append(', ')->append($this->getRecord()->unit->name))
            ->wrap('<div class="fi-header-subheading">', '</div>')
            ->toHtmlString();
    }

    /**
     * @return ViewComponent[]
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('email')
                ->hidden(fn (): bool => in_array('email', Arr::wrap(SettingsService::get(DashboardSettings::class, 'user_hidden_fields', []))))
                ->color('gray')
                ->url(fn (?User $record): string => 'mailto:'.$record->email)
                ->openUrlInNewTab()
                ->tooltip(fn (?User $record) => $record->email),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
