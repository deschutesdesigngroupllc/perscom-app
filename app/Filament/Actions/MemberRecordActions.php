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
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
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

    public static function group(): ActionGroup
    {
        return ActionGroup::make(static::make())
            ->label('Add Record')
            ->tooltip('Add Record');
    }

    /**
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
}
