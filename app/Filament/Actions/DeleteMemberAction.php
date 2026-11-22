<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;

/**
 * Deleting a member also deletes every record in their service history, so the confirmation states
 * what will be lost, points to archiving instead, and requires typing the member's name.
 */
class DeleteMemberAction extends DeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->modalHeading(fn (User $record): string => sprintf('Delete %s', $record->name))
            ->modalDescription(function (User $record): string {
                $count = static::serviceRecordCount($record);

                return sprintf(
                    "This permanently deletes their profile and %d %s in their service history. This can't be undone. To keep their history, archive them instead.",
                    $count,
                    Str::plural('record', $count),
                );
            })
            ->modalSubmitActionLabel('Delete Member')
            ->schema([
                TextInput::make('confirmation')
                    ->label(fn (User $record): string => sprintf('Type "%s" to confirm', $record->name))
                    ->required()
                    ->in(fn (User $record): array => [$record->name])
                    ->validationMessages(['in' => "The name you typed doesn't match."])
                    ->autocomplete(false),
            ]);
    }

    public static function serviceRecordCount(User $user): int
    {
        return collect(['assignment_records', 'award_records', 'combat_records', 'qualification_records', 'rank_records', 'service_records', 'training_records'])
            ->sum(fn (string $relation): int => $user->{$relation}()->count());
    }
}
