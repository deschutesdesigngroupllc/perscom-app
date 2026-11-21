<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Filament\Actions\Concerns\AppliesChangeSets;
use App\Models\Enums\RankRecordType;
use App\Models\RankRecord;
use App\Models\User;
use App\Services\PromotionEligibilityService;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

/**
 * Promote a single member, defaulting to the next rank above their current one. Works from a member's profile, their row in the members table, or a
 * relation manager on their profile, and applies the same audited change set as PromoteUsersBulkAction.
 */
class PromoteMemberAction extends Action
{
    use AppliesChangeSets;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Promote')
            ->icon(Heroicon::OutlinedChevronDoubleUp)
            ->modalHeading(fn (?Model $record, Component $livewire): string => sprintf('Promote %s', static::resolveMember($record, $livewire)?->name))
            ->modalDescription('Adds a rank record to their profile. They are notified as usual.')
            ->modalSubmitActionLabel('Promote')
            ->visible(fn (): bool => Gate::check('create', RankRecord::class))
            ->schema(PromoteUsersBulkAction::formSchema())
            ->fillForm(function (?Model $record, Component $livewire): array {
                $rank = static::resolveMember($record, $livewire)?->rank;

                return [
                    'rank_id' => $rank ? PromotionEligibilityService::nextRankFor($rank)?->getKey() : null,
                    'type' => RankRecordType::PROMOTION,
                ];
            })
            ->action(function (Action $action, ?Model $record, Component $livewire, array $data): void {
                $member = static::resolveMember($record, $livewire);

                if (! $member instanceof User) {
                    return;
                }

                $this->applyOperation($action, PromoteUsersBulkAction::operationFor(collect([$member]), $data), 'profile action');
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'promoteMember';
    }

    public static function resolveMember(?Model $record, Component $livewire): ?User
    {
        if ($record instanceof User) {
            return $record;
        }

        $owner = method_exists($livewire, 'getOwnerRecord') ? $livewire->getOwnerRecord() : null;

        return $owner instanceof User ? $owner : null;
    }
}
