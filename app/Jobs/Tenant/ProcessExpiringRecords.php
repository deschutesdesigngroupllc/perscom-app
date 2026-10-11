<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Contracts\RunsPerTenant;
use App\Events\Automations\ScheduledTriggerFired;
use App\Filament\App\Resources\QualificationRecordResource;
use App\Filament\App\Resources\TrainingRecordResource;
use App\Jobs\Concerns\RunsForTenant;
use App\Models\Credential;
use App\Models\Enums\AutomationTrigger;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use App\Models\Scopes\QualificationRecordScope;
use App\Models\TrainingRecordCredential;
use App\Models\User;
use App\Notifications\Tenant\CredentialExpiring;
use Carbon\CarbonInterface;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Notification;

class ProcessExpiringRecords implements RunsPerTenant, ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;
    use RunsForTenant;

    /**
     * The number of days ahead to look for expiring records.
     */
    public const int LOOKAHEAD_DAYS = 30;

    public function __construct(public ?int $tenantKey = null)
    {
        $this->configureForTenancy();
    }

    public static function daysUntil(CarbonInterface $expiresAt): int
    {
        return (int) today()->diffInDays($expiresAt->copy()->startOfDay(), false);
    }

    public function work(): void
    {
        $this->processCredentials();
        $this->processQualifications();
    }

    protected function processCredentials(): void
    {
        TrainingRecordCredential::query()
            ->with(['credential', 'training_record'])
            ->whereBetween('expires_at', [today(), today()->addDays(self::LOOKAHEAD_DAYS)->endOfDay()])
            ->chunkById(100, fn (Collection $credentials) => $credentials->each(function (TrainingRecordCredential $pivot): void {
                $credential = $pivot->credential;
                $trainingRecord = $pivot->training_record;
                $user = $trainingRecord?->user;

                if (! $credential instanceof Credential || ! $user instanceof User || ! $user->exists || $this->credentialIsSuperseded($pivot, $user)) {
                    return;
                }

                $expiresAt = Date::parse($pivot->getAttribute('expires_at'));
                $days = self::daysUntil($expiresAt);

                event(new ScheduledTriggerFired(
                    subject: $pivot,
                    trigger: $days <= 0 ? AutomationTrigger::CREDENTIAL_EXPIRED : AutomationTrigger::CREDENTIAL_EXPIRING,
                    additionalContext: [
                        'user_id' => $user->getKey(),
                        'credential_name' => $credential->name,
                        'days_until_expiry' => $days,
                    ],
                ));

                if (CredentialExpiring::shouldRemind($days)) {
                    Notification::send($user, new CredentialExpiring(
                        name: $credential->name,
                        expiresAt: $expiresAt,
                        daysUntilExpiry: $days,
                        url: TrainingRecordResource::getUrl('view', ['record' => $trainingRecord], panel: 'app'),
                    ));
                }
            }));
    }

    protected function processQualifications(): void
    {
        QualificationRecord::query()
            ->withoutGlobalScope(QualificationRecordScope::class)
            ->with('qualification')
            ->whereBetween('expires_at', [today(), today()->addDays(self::LOOKAHEAD_DAYS)->endOfDay()])
            ->chunkById(100, fn (Collection $records) => $records->each(function (QualificationRecord $record): void {
                $qualification = $record->qualification;
                $user = $record->user;

                if (! $qualification instanceof Qualification || ! $user->exists || $this->qualificationIsSuperseded($record)) {
                    return;
                }

                $expiresAt = Date::parse($record->getAttribute('expires_at'));
                $days = self::daysUntil($expiresAt);

                event(new ScheduledTriggerFired(
                    subject: $record,
                    trigger: $days <= 0 ? AutomationTrigger::QUALIFICATION_RECORD_EXPIRED : AutomationTrigger::QUALIFICATION_RECORD_EXPIRING,
                    additionalContext: [
                        'qualification_name' => $qualification->name,
                        'days_until_expiry' => $days,
                    ],
                ));

                if (CredentialExpiring::shouldRemind($days)) {
                    Notification::send($user, new CredentialExpiring(
                        name: $qualification->name,
                        expiresAt: $expiresAt,
                        daysUntilExpiry: $days,
                        kind: 'qualification',
                        url: QualificationRecordResource::getUrl('view', ['record' => $record], panel: 'app'),
                    ));
                }
            }));
    }

    /**
     * A credential is superseded when the user holds a newer grant of the same credential that expires later or never.
     */
    protected function credentialIsSuperseded(TrainingRecordCredential $pivot, User $user): bool
    {
        return TrainingRecordCredential::query()
            ->where('credential_id', $pivot->getAttribute('credential_id'))
            ->whereKeyNot($pivot->getKey())
            ->whereHas('training_record', fn (Builder $query): Builder => $query->whereBelongsTo($user))
            ->where(fn (Builder $query): Builder => $query->whereNull('expires_at')->orWhere('expires_at', '>', $pivot->getAttribute('expires_at')))
            ->exists();
    }

    /**
     * A qualification is superseded when the user holds a newer record of the same qualification that expires later or never.
     */
    protected function qualificationIsSuperseded(QualificationRecord $record): bool
    {
        return QualificationRecord::query()
            ->withoutGlobalScope(QualificationRecordScope::class)
            ->where('user_id', $record->user_id)
            ->where('qualification_id', $record->qualification_id)
            ->whereKeyNot($record->getKey())
            ->where(fn (Builder $query): Builder => $query->whereNull('expires_at')->orWhere('expires_at', '>', $record->getAttribute('expires_at')))
            ->exists();
    }
}
