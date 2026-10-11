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
use Illuminate\Validation\ValidationException;

class InviteAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Invite')
            ->icon(Heroicon::OutlinedEnvelope)
            ->modalHeading('Invite Personnel')
            ->modalDescription('Send an email invitation that allows the recipient to create their own account and profile.')
            ->modalSubmitActionLabel('Send Invite')
            ->authorize('create', Invite::class)
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(User::class, 'email')
                    ->validationMessages([
                        'unique' => 'A user with this email address already exists.',
                    ]),
                TextInput::make('name')
                    ->label('Name')
                    ->helperText('Optional. Pre-fills the name on the registration form.')
                    ->maxLength(255),
            ])
            ->successNotificationTitle('The invitation has been sent.')
            ->action(function (Action $action, array $data): void {
                try {
                    SendInvite::handle(
                        type: InviteType::CREATE,
                        email: $data['email'],
                        name: $data['name'] ?? null,
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
        return 'invite';
    }
}
