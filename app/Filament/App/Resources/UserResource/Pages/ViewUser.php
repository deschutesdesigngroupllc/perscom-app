<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\Actions\ArchiveMemberAction;
use App\Filament\Actions\ClaimInviteAction;
use App\Filament\Actions\DeleteMemberAction;
use App\Filament\Actions\MemberRecordActions;
use App\Filament\App\Resources\UserResource;
use App\Filament\App\Resources\UserResource\RelationManagers\AssignmentRecordsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\AwardRecordsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\CombatRecordsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\QualificationRecordsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\RankRecordsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\ServiceRecordsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\TrainingRecordsRelationManager;
use App\Models\User;
use App\Services\ServiceProfileService;
use App\Services\SettingsService;
use App\Settings\DashboardSettings;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Get the record tabs the organization shows on member profiles, in order.
     *
     * @return array<int, class-string<RelationManager>>
     */
    public static function profileRelationManagers(): array
    {
        $hiddenFields = Arr::wrap(SettingsService::get(DashboardSettings::class, 'user_hidden_fields', []));

        return collect([
            'assignment_records' => AssignmentRecordsRelationManager::class,
            'award_records' => AwardRecordsRelationManager::class,
            'combat_records' => CombatRecordsRelationManager::class,
            'qualification_records' => QualificationRecordsRelationManager::class,
            'rank_records' => RankRecordsRelationManager::class,
            'service_records' => ServiceRecordsRelationManager::class,
            'training_records' => TrainingRecordsRelationManager::class,
        ])
            ->reject(fn (string $relationManager, string $field): bool => in_array($field, $hiddenFields, true))
            ->values()
            ->all();
    }

    public function getSubheading(): string|Htmlable|null
    {
        return new Stringable()
            /** @phpstan-ignore-next-line property.notFound */
            ->when($this->getRecord()->rank, fn (Stringable $str) => $str->append(' ')->append($this->getRecord()->rank->paygrade)->append(', ')->append($this->getRecord()->rank->name))
            /** @phpstan-ignore-next-line property.notFound */
            ->when($this->getRecord()->position, fn (Stringable $str) => $str->append(', ')->append($this->getRecord()->position->name))
            /** @phpstan-ignore-next-line property.notFound */
            ->when($this->getRecord()->unit, fn (Stringable $str) => $str->append(', ')->append($this->getRecord()->unit->name))
            ->wrap('<div class="fi-header-subheading">', '</div>')
            ->toHtmlString();
    }

    /**
     * Get the profile record tabs; Filament drops those the viewer cannot see.
     *
     * @return array<int, class-string<RelationManager>>
     */
    protected function getAllRelationManagers(): array
    {
        return static::profileRelationManagers();
    }

    /**
     * @return ViewComponent[]
     */
    protected function getHeaderActions(): array
    {
        return [
            MemberRecordActions::group()
                ->icon(Heroicon::OutlinedPlus)
                ->button(),
            ClaimInviteAction::make()
                ->color('gray'),
            Action::make('publicProfile')
                ->label('Public Profile')
                ->icon(Heroicon::OutlinedGlobeAlt)
                ->color('gray')
                ->visible(fn (?User $record): bool => $record instanceof User && ServiceProfileService::isPublic($record))
                ->url(fn (User $record): string => ServiceProfileService::url($record))
                ->openUrlInNewTab(),
            Action::make('email')
                ->hidden(fn (): bool => in_array('email', Arr::wrap(SettingsService::get(DashboardSettings::class, 'user_hidden_fields', []))))
                ->color('gray')
                ->url(fn (?User $record): string => 'mailto:'.$record->email)
                ->openUrlInNewTab()
                ->tooltip(fn (?User $record) => $record->email),
            EditAction::make()
                ->color('gray'),
            ActionGroup::make([
                ArchiveMemberAction::make(),
                DeleteMemberAction::make(),
            ])
                ->tooltip('More')
                ->color('gray')
                ->button()
                ->label('More')
                ->icon(Heroicon::OutlinedEllipsisHorizontal),
        ];
    }
}
