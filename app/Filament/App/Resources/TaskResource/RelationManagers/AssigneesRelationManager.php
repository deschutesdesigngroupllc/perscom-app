<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\TaskResource\RelationManagers;

use App\Filament\App\Resources\UserResource;
use App\Filament\Forms\Components\MemberSelect;
use App\Models\Task;
use App\Models\User;
use App\Services\UserSettingsService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DetachAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Who a task is assigned to, when it's due and whether they finished it.
 */
class AssigneesRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Assignees';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedUsers;

    public static function statusFor(User $user): string
    {
        $assignment = $user->getRelationValue('assignment');

        return match (true) {
            filled($assignment?->completed_at) => 'Completed',
            filled($assignment?->due_at) && $assignment->due_at->isPast() => 'Overdue',
            filled($assignment?->due_at) => 'Due',
            default => 'Open',
        };
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->description('Members assigned to this task and where each one stands.')
            ->recordTitleAttribute('name')
            ->defaultSort('users.name')
            ->columns([
                TextColumn::make('name')
                    ->label('Member')
                    ->url(fn (User $record): string => UserResource::getUrl('view', ['record' => $record]))
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->state(fn (User $record): string => static::statusFor($record))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Completed' => 'success',
                        'Overdue' => 'danger',
                        'Due' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('due')
                    ->label('Due')
                    ->state(fn (User $record) => $record->getRelationValue('assignment')?->due_at)
                    ->dateTime('M j, Y g:i A')
                    ->timezone(fn (): string => UserSettingsService::timezone())
                    ->placeholder('No due date'),
                TextColumn::make('completed')
                    ->label('Completed')
                    ->state(fn (User $record) => $record->getRelationValue('assignment')?->completed_at)
                    ->date()
                    ->placeholder('Not yet'),
            ])
            ->headerActions([
                Action::make('assignMembers')
                    ->label('Assign Members')
                    ->icon(Heroicon::OutlinedUserPlus)
                    ->modalHeading('Assign Members')
                    ->modalDescription('Each member is notified about the task.')
                    ->modalSubmitActionLabel('Assign')
                    ->schema([
                        MemberSelect::make('users')
                            ->label('Members')
                            ->multiple()
                            ->required(),
                        DateTimePicker::make('due_at')
                            ->label('Due')
                            ->timezone(fn (): string => UserSettingsService::timezone())
                            ->helperText('Optional.'),
                    ])
                    ->successNotificationTitle('Members Assigned')
                    ->action(function (Action $action, array $data): void {
                        /** @var Task $task */
                        $task = $this->getOwnerRecord();

                        $task->users()->syncWithoutDetaching(collect($data['users'])->mapWithKeys(fn (int|string $id): array => [(int) $id => [
                            'assigned_by_id' => Auth::id(),
                            'assigned_at' => now(),
                            'due_at' => $data['due_at'] ?? null,
                        ]])->all());

                        $action->success();
                    }),
            ])
            ->recordActions([
                Action::make('markComplete')
                    ->label('Mark Complete')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (User $record): bool => blank($record->getRelationValue('assignment')?->completed_at))
                    ->successNotificationTitle('Marked Complete')
                    ->action(function (Action $action, User $record): void {
                        /** @var Task $task */
                        $task = $this->getOwnerRecord();

                        $task->users()->updateExistingPivot($record->getKey(), ['completed_at' => now()]);

                        $action->success();
                    }),
                DetachAction::make()
                    ->label('Remove'),
            ])
            ->emptyStateIcon(Heroicon::OutlinedUsers)
            ->emptyStateHeading('Nobody Is Assigned Yet')
            ->emptyStateDescription('Assign members to send them the task and track who finishes it.');
    }
}
