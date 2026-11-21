<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Filament\Actions\Concerns\AppliesChangeSets;
use App\Models\AssignmentRecord;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

/**
 * Assign a single member to a unit, position and specialty. Works from a member's profile, their row in the members table, or a
 * relation manager on their profile, and applies the same audited change set as AssignUsersBulkAction.
 */
class AssignMemberAction extends Action
{
    use AppliesChangeSets;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Assign')
            ->icon(Heroicon::OutlinedRectangleGroup)
            ->modalHeading(fn (?Model $record, Component $livewire): string => sprintf('Assign %s', static::resolveMember($record, $livewire)?->name))
            ->modalDescription('Adds an assignment record to their profile. They are notified as usual.')
            ->modalSubmitActionLabel('Assign')
            ->visible(fn (): bool => Gate::check('create', AssignmentRecord::class))
            ->schema(AssignUsersBulkAction::formSchema())
            ->action(function (Action $action, ?Model $record, Component $livewire, array $data): void {
                $member = static::resolveMember($record, $livewire);

                if (! $member instanceof User) {
                    return;
                }

                $this->applyOperation($action, AssignUsersBulkAction::operationFor(collect([$member]), $data), 'profile action');
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'assignMember';
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
