<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Actions\Invites\ResendInvite;
use App\Filament\App\Resources\InviteResource\Pages\ListInvites;
use App\Filament\App\Resources\UserResource\Pages\ViewUser;
use App\Models\Enums\InviteStatus;
use App\Models\Enums\InviteType;
use App\Models\Invite;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\PageRegistration;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use UnitEnum;

class InviteResource extends BaseResource
{
    protected static ?string $model = Invite::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static string|UnitEnum|null $navigationGroup = 'Personnel';

    protected static ?string $navigationParentItem = 'Users';

    protected static ?int $navigationSort = 7;

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateDescription('There are no invitations to view. Invite personnel from the users page.')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('name')
                    ->placeholder('No Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('user.name')
                    ->label('Profile')
                    ->placeholder('Not Created')
                    ->url(fn (Invite $record): ?string => $record->user ? ViewUser::getUrl(['record' => $record->user]) : null),
                TextColumn::make('invitedBy.name')
                    ->label('Invited By')
                    ->placeholder('System')
                    ->toggleable(),
                TextColumn::make('sent_at')
                    ->label('Sent')
                    ->dateTime()
                    ->since()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('accepted_at')
                    ->label('Accepted')
                    ->placeholder('Not Accepted')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(InviteType::class),
                SelectFilter::make('status')
                    ->options(InviteStatus::class)
                    ->query(fn (Builder $query, array $data): Builder => match (InviteStatus::tryFrom((string) $data['value'])) {
                        InviteStatus::PENDING => $query->scopes(['pending']),
                        InviteStatus::ACCEPTED => $query->whereNotNull('accepted_at'),
                        InviteStatus::REVOKED => $query->whereNull('accepted_at')->whereNotNull('revoked_at'),
                        InviteStatus::EXPIRED => $query->whereNull('accepted_at')->whereNull('revoked_at')->where('expires_at', '<=', now()),
                        default => $query,
                    }),
            ])
            ->recordActions([
                Action::make('resend')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->requiresConfirmation()
                    ->modalHeading('Resend Invitation')
                    ->modalDescription('A new invitation link will be emailed and any previously sent link will stop working.')
                    ->authorize('update')
                    ->visible(fn (Invite $record): bool => $record->status !== InviteStatus::ACCEPTED && ($record->type === InviteType::CREATE || (filled($record->user) && blank($record->user->password))))
                    ->successNotificationTitle('The invitation has been resent.')
                    ->action(function (Action $action, Invite $record): void {
                        try {
                            ResendInvite::handle($record);
                        } catch (ValidationException $validationException) {
                            $action->failureNotificationTitle($validationException->validator->errors()->first())->failure();

                            return;
                        }

                        $action->success();
                    }),
                Action::make('revoke')
                    ->icon(Heroicon::OutlinedNoSymbol)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Revoke Invitation')
                    ->modalDescription('The invitation link will immediately stop working.')
                    ->authorize('update')
                    ->visible(fn (Invite $record): bool => $record->isPending())
                    ->successNotificationTitle('The invitation has been revoked.')
                    ->action(function (Action $action, Invite $record): void {
                        $record->revoke();

                        $action->success();
                    }),
                DeleteAction::make(),
            ]);
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListInvites::route('/'),
        ];
    }
}
