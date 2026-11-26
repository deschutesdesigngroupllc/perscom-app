<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Filament\Actions\Concerns\AppliesChangeSets;
use App\Models\QualificationRecord;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

use function sprintf;

class QualifyMemberAction extends Action
{
    use AppliesChangeSets;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Qualify')
            ->icon(Heroicon::OutlinedStar)
            ->modalHeading(fn (?Model $record, Component $livewire): string => sprintf('Qualify %s', PromoteMemberAction::resolveMember($record, $livewire)?->name))
            ->modalDescription('Adds a qualification record to their profile. They are notified as usual.')
            ->modalSubmitActionLabel('Add Qualification')
            ->visible(fn (): bool => Gate::check('create', QualificationRecord::class))
            ->schema(QualifyUsersBulkAction::formSchema())
            ->action(function (Action $action, ?Model $record, Component $livewire, array $data): void {
                $member = PromoteMemberAction::resolveMember($record, $livewire);

                if (! $member instanceof User) {
                    return;
                }

                $this->applyOperation($action, QualifyUsersBulkAction::operationFor(collect([$member]), $data), 'profile action');
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'qualifyMember';
    }
}
