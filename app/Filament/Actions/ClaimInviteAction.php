<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\Invites\SendInvite;
use App\Models\Enums\InviteType;
use App\Models\Invite;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\ValidationException;

class ClaimInviteAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Send Invite')
            ->icon(Heroicon::OutlinedEnvelope)
            ->modalHeading('Invite To Claim Profile')
            ->modalDescription('Send an email invitation that allows the recipient to set a password and take ownership of this profile. Any outstanding invitations for this profile will be revoked.')
            ->modalSubmitActionLabel('Send Invite')
            ->visible(fn (?User $record): bool => $record instanceof User && blank($record->password) && Gate::check('create', Invite::class))
            ->fillForm(fn (User $record): array => [
                'email' => $record->email,
            ])
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->helperText('The profile email will be updated to this address once the invitation is accepted.')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(User::class, 'email', modifyRuleUsing: fn (Unique $rule, User $record): Unique => $rule->ignore($record->getKey()))
                    ->validationMessages([
                        'unique' => 'A different user with this email address already exists.',
                    ]),
            ])
            ->successNotificationTitle('The invitation has been sent.')
            ->action(function (Action $action, User $record, array $data): void {
                try {
                    SendInvite::handle(
                        type: InviteType::CLAIM,
                        email: $data['email'],
                        user: $record,
                        invitedBy: Auth::user(),
                    );
                } catch (ValidationException $validationException) {
                    $action->failureNotificationTitle($validationException->validator->errors()->first())->failure();

                    return;
                }

                $action->success();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'sendInvite';
    }
}
