<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Filament\Actions\Concerns\AppliesChangeSets;
use App\Models\AwardRecord;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

use function sprintf;

class AwardMemberAction extends Action
{
    use AppliesChangeSets;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Award')
            ->icon(Heroicon::OutlinedTrophy)
            ->modalHeading(fn (?Model $record, Component $livewire): string => sprintf('Award %s', static::resolveMember($record, $livewire)?->name))
            ->modalDescription('Adds an award record to their profile. They are notified as usual.')
            ->modalSubmitActionLabel('Give Award')
            ->visible(fn (): bool => Gate::check('create', AwardRecord::class))
            ->schema(AwardUsersBulkAction::formSchema())
            ->action(function (Action $action, ?Model $record, Component $livewire, array $data): void {
                $member = static::resolveMember($record, $livewire);

                if (! $member instanceof User) {
                    return;
                }

                $this->applyOperation($action, AwardUsersBulkAction::operationFor(collect([$member]), $data), 'profile action');
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'awardMember';
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
