<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Filament\App\Resources\CombatRecordResource;
use App\Filament\App\Resources\ServiceRecordResource;
use App\Filament\App\Resources\TrainingRecordResource;
use App\Models\CombatRecord;
use App\Models\ServiceRecord;
use App\Models\TrainingRecord;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

/**
 * The "Add Record" menu for a member: one entry per record type. Promote, Award, Assign and Qualify
 * open a short dialog; training, combat and service records have richer forms, so those open the full
 * form with the member already filled in.
 */
class MemberRecordActions
{
    /**
     * Build the record actions for a single member.
     *
     * @return array<int, Action>
     */
    public static function make(): array
    {
        return [
            PromoteMemberAction::make(),
            AwardMemberAction::make(),
            AssignMemberAction::make(),
            QualifyMemberAction::make(),
            static::linkToCreate('recordTraining', 'Record Training', Heroicon::OutlinedAcademicCap, TrainingRecord::class, fn (int $userId): string => TrainingRecordResource::getUrl('create', ['user_id' => $userId])),
            static::linkToCreate('recordCombat', 'Record Combat', Heroicon::OutlinedFire, CombatRecord::class, fn (int $userId): string => CombatRecordResource::getUrl('create', ['user_id' => $userId])),
            static::linkToCreate('recordService', 'Record Service', Heroicon::OutlinedDocumentText, ServiceRecord::class, fn (int $userId): string => ServiceRecordResource::getUrl('create', ['user_id' => $userId])),
        ];
    }

    /**
     * The same record types for members selected in a table. Training, combat and service open the
     * full form with every selected member filled in.
     *
     * @return array<int, BulkAction>
     */
    public static function bulk(): array
    {
        return [
            PromoteUsersBulkAction::make(),
            AwardUsersBulkAction::make(),
            AssignUsersBulkAction::make(),
            QualifyUsersBulkAction::make(),
            static::bulkLinkToCreate('recordTrainingBulk', 'Record Training', Heroicon::OutlinedAcademicCap, TrainingRecord::class, fn (array $userIds): string => TrainingRecordResource::getUrl('create', ['user_id' => $userIds])),
            static::bulkLinkToCreate('recordCombatBulk', 'Record Combat', Heroicon::OutlinedFire, CombatRecord::class, fn (array $userIds): string => CombatRecordResource::getUrl('create', ['user_id' => $userIds])),
            static::bulkLinkToCreate('recordServiceBulk', 'Record Service', Heroicon::OutlinedDocumentText, ServiceRecord::class, fn (array $userIds): string => ServiceRecordResource::getUrl('create', ['user_id' => $userIds])),
        ];
    }

    public static function group(): ActionGroup
    {
        return ActionGroup::make(static::make())
            ->label('Add Record')
            ->tooltip('Add Record');
    }

    /**
     * Build an action that opens a record's create form for the member.
     *
     * @param  class-string<Model>  $recordModel
     * @param  callable(int): string  $url
     */
    protected static function linkToCreate(string $name, string $label, Heroicon $icon, string $recordModel, callable $url): Action
    {
        return Action::make($name)
            ->label($label)
            ->icon($icon)
            ->visible(fn (): bool => Gate::check('create', $recordModel))
            ->url(function (?Model $record, Component $livewire) use ($url): ?string {
                $member = PromoteMemberAction::resolveMember($record, $livewire);

                return $member instanceof User ? $url($member->getKey()) : null;
            });
    }

    /**
     * Build a bulk action that opens a record's create form for the members.
     *
     * @param  class-string<Model>  $recordModel
     * @param  callable(array<int, int>): string  $url
     */
    protected static function bulkLinkToCreate(string $name, string $label, Heroicon $icon, string $recordModel, callable $url): BulkAction
    {
        return BulkAction::make($name)
            ->label($label)
            ->icon($icon)
            ->visible(fn (): bool => Gate::check('create', $recordModel))
            ->action(function (BulkAction $action, Collection $records) use ($url): void {
                $action->redirect($url($records->map(fn (User $user): int => $user->getKey())->sort()->values()->all()));
            });
    }
}
