<?php

declare(strict_types=1);

namespace App\Filament\Tables\Columns;

use App\Models\Credential;
use App\Models\TrainingRecord;
use Carbon\CarbonInterface;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Date;

class ExpiresAtColumn extends TextColumn
{
    /**
     * The number of days before expiration that a record is considered expiring.
     */
    public const int EXPIRING_WITHIN_DAYS = 30;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Expires')
            ->badge()
            ->placeholder('Never')
            ->formatStateUsing(fn (mixed $state): string => static::labelFor($state))
            ->color(fn (mixed $state): string => static::colorFor($state));
    }

    public static function getDefaultName(): ?string
    {
        return 'expires_at';
    }

    public static function resolveDate(mixed $state): ?CarbonInterface
    {
        if (blank($state)) {
            return null;
        }

        return $state instanceof CarbonInterface ? $state : Date::parse($state);
    }

    public static function colorFor(mixed $state): string
    {
        $date = static::resolveDate($state);

        return match (true) {
            ! $date instanceof CarbonInterface => 'gray',
            $date->isPast() => 'danger',
            $date->lessThanOrEqualTo(now()->addDays(static::EXPIRING_WITHIN_DAYS)) => 'warning',
            default => 'success',
        };
    }

    public static function labelFor(mixed $state): string
    {
        $date = static::resolveDate($state);

        if (! $date instanceof CarbonInterface) {
            return 'Never';
        }

        return $date->isPast()
            ? 'Expired '.$date->toFormattedDateString()
            : $date->toFormattedDateString();
    }

    /**
     * A column listing a training record's credentials, each colored by its expiration.
     */
    public static function makeForCredentials(string $name = 'credential_expirations'): static
    {
        return static::make($name)
            ->label('Credentials')
            ->placeholder('No Credentials')
            ->listWithLineBreaks()
            ->state(fn (TrainingRecord $record): array => $record->credentials
                ->map(fn (Credential $credential): string => sprintf(
                    '%s|%s',
                    $credential->name,
                    static::resolveDate($credential->pivot->getAttribute('expires_at'))?->toIso8601String(),
                ))
                ->all())
            ->formatStateUsing(function (string $state): string {
                [$name, $expiresAt] = static::splitCredentialState($state);

                return blank($expiresAt) ? $name : sprintf('%s (%s)', $name, static::labelFor($expiresAt));
            })
            ->color(fn (string $state): string => static::colorFor(static::splitCredentialState($state)[1]));
    }

    /**
     * Split a credential state into its name and expiration. The expiration is always the last
     * segment, so a credential name that itself contains the delimiter is preserved intact.
     *
     * @return array{0: string, 1: ?string}
     */
    public static function splitCredentialState(string $state): array
    {
        $position = strrpos($state, '|');

        if ($position === false) {
            return [$state, null];
        }

        return [substr($state, 0, $position), substr($state, $position + 1)];
    }
}
