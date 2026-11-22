<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\Invites\SendInvite;
use App\Models\Enums\InviteType;
use App\Models\Invite;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InviteAction extends Action
{
    public const int MAX_INVITES = 50;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Invite')
            ->icon(Heroicon::OutlinedEnvelope)
            ->modalHeading('Invite Members')
            ->modalDescription('Each person gets an email to create their own account and profile.')
            ->modalSubmitActionLabel('Send Invitations')
            ->authorize('create', Invite::class)
            ->schema([
                Textarea::make('emails')
                    ->label('Email Addresses')
                    ->placeholder("jordan@example.com\nsam@example.com")
                    ->helperText(sprintf('One per line, or separated by commas. Up to %d at a time.', static::MAX_INVITES))
                    ->rows(5)
                    ->required()
                    ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                        $parsed = static::parseEmails((string) $value);

                        if (blank($parsed['valid']) && blank($parsed['invalid'])) {
                            $fail('Enter at least one email address.');
                        } elseif (count($parsed['valid']) + count($parsed['invalid']) > static::MAX_INVITES) {
                            $fail(sprintf('Invite up to %d people at a time.', static::MAX_INVITES));
                        }
                    }),
            ])
            ->action(function (Action $action, array $data): void {
                $parsed = static::parseEmails((string) $data['emails']);
                $skipped = collect($parsed['invalid'])->map(fn (string $email): string => sprintf('%s (not a valid email)', $email));
                $sent = 0;

                collect($parsed['valid'])->each(function (string $email) use (&$sent, $skipped): void {
                    try {
                        SendInvite::handle(type: InviteType::CREATE, email: $email, invitedBy: Auth::user());
                        $sent++;
                    } catch (ValidationException $validationException) {
                        $skipped->push(sprintf('%s (%s)', $email, rtrim(Str::lcfirst((string) $validationException->validator->errors()->first()), '.')));
                    }
                });

                $notification = Notification::make()
                    ->title($sent > 0 ? sprintf('Sent %d %s', $sent, Str::plural('Invitation', $sent)) : 'No Invitations Sent')
                    ->body($skipped->isNotEmpty() ? 'Skipped: '.$skipped->implode('; ').'.' : null);

                ($sent > 0 ? $notification->success() : $notification->danger())->send();

                if ($sent === 0) {
                    $action->halt();
                }
            });
    }

    /**
     * @return array{valid: array<int, string>, invalid: array<int, string>}
     */
    public static function parseEmails(string $input): array
    {
        $entries = collect(preg_split('/[\s,;]+/', $input) ?: [])
            ->map(fn (string $entry): string => Str::lower(trim($entry, " \t\n\r\0\x0B<>\"'")))
            ->filter()
            ->unique()
            ->values();

        return [
            'valid' => $entries->filter(fn (string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false)->values()->all(),
            'invalid' => $entries->reject(fn (string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false)->values()->all(),
        ];
    }

    public static function getDefaultName(): ?string
    {
        return 'invite';
    }
}
