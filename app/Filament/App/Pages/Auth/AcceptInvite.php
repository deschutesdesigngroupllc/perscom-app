<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Auth;

use App\Actions\Invites\AcceptInvite as AcceptInviteAction;
use App\Models\Enums\InviteType;
use App\Models\Invite;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;

/**
 * @property-read Schema $form
 */
class AcceptInvite extends SimplePage
{
    use WithRateLimiting;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    #[Locked]
    public ?int $inviteId = null;

    public function mount(string $token): void
    {
        $invite = Invite::findByToken($token);

        $this->inviteId = $invite?->isRedeemable() ? $invite->getKey() : null;

        $this->form->fill([
            'email' => $invite?->email,
            'name' => $invite?->name,
        ]);
    }

    public function accept(): void
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $tooManyRequestsException) {
            Notification::make()
                ->title(__('Too many attempts. Please try again in :seconds seconds.', ['seconds' => $tooManyRequestsException->secondsUntilAvailable]))
                ->danger()
                ->send();

            return;
        }

        $invite = $this->getInvite();

        if (! $invite instanceof Invite) {
            return;
        }

        $data = $this->form->getState();

        try {
            $user = AcceptInviteAction::handle($invite, $data);
        } catch (ValidationException $validationException) {
            if (! $invite->refresh()->isRedeemable()) {
                $this->inviteId = null;

                return;
            }

            throw ValidationException::withMessages(collect($validationException->errors())
                ->mapWithKeys(fn (array $messages, string $key): array => ["data.$key" => $messages])
                ->all());
        }

        Filament::auth()->login($user);

        session()->regenerate();

        $this->redirect(Filament::getUrl());
    }

    public function getInvite(): ?Invite
    {
        return $this->inviteId ? Invite::query()->with('user')->find($this->inviteId) : null;
    }

    public function isClaim(): bool
    {
        return $this->getInvite()?->type === InviteType::CLAIM;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                TextInput::make('email')
                    ->label(__('Email address'))
                    ->email()
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus()
                    ->hidden(fn (): bool => $this->isClaim()),
                TextInput::make('password')
                    ->label(__('Password'))
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->rule(Password::min(8))
                    ->same('password_confirmation')
                    ->validationAttribute(__('password')),
                TextInput::make('password_confirmation')
                    ->label(__('Confirm password'))
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required(),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        if (! $this->getInvite() instanceof Invite) {
            return $schema->components([
                Text::make(__('This invitation is invalid, has expired, or has already been used. Please contact an administrator to request a new invitation.')),
                Actions::make([
                    Action::make('login')
                        ->label(__('Go to login'))
                        ->url(Filament::getLoginUrl()),
                ])->fullWidth(),
            ]);
        }

        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('accept')
                ->footer([
                    Actions::make([
                        Action::make('accept')
                            ->label($this->isClaim() ? __('Claim profile') : __('Create account'))
                            ->submit('accept'),
                    ])->fullWidth(),
                ]),
        ]);
    }

    public function getTitle(): string|Htmlable
    {
        return $this->isClaim() ? __('Claim Your Profile') : __('Accept Invitation');
    }

    public function getHeading(): string|Htmlable|null
    {
        return $this->getTitle();
    }

    public function getSubheading(): string|Htmlable|null
    {
        $invite = $this->getInvite();

        if (! $invite instanceof Invite) {
            return null;
        }

        return $this->isClaim()
            ? __('Set a password to take ownership of the profile for :name.', ['name' => $invite->user->name ?? $invite->name])
            : __('Finish setting up your account to get started.');
    }
}
