<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\TrainingRecordCredentialObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int|null $training_record_id
 * @property int|null $credential_id
 * @property \Illuminate\Support\Carbon|null $issued_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property-read Credential|null $credential
 * @property-read TrainingRecord|null $training_record
 *
 * @method static Builder<static>|TrainingRecordCredential expired()
 * @method static Builder<static>|TrainingRecordCredential expiringWithin(int $days)
 * @method static Builder<static>|TrainingRecordCredential newModelQuery()
 * @method static Builder<static>|TrainingRecordCredential newQuery()
 * @method static Builder<static>|TrainingRecordCredential query()
 * @method static Builder<static>|TrainingRecordCredential whereCredentialId($value)
 * @method static Builder<static>|TrainingRecordCredential whereExpiresAt($value)
 * @method static Builder<static>|TrainingRecordCredential whereId($value)
 * @method static Builder<static>|TrainingRecordCredential whereIssuedAt($value)
 * @method static Builder<static>|TrainingRecordCredential whereTrainingRecordId($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
#[ObservedBy(TrainingRecordCredentialObserver::class)]
class TrainingRecordCredential extends Pivot
{
    public $incrementing = true;

    protected $table = 'records_trainings_credentials';

    protected $fillable = [
        'training_record_id',
        'credential_id',
        'issued_at',
        'expires_at',
    ];

    /**
     * @return BelongsTo<TrainingRecord, $this>
     */
    public function training_record(): BelongsTo
    {
        return $this->belongsTo(TrainingRecord::class);
    }

    /**
     * @return BelongsTo<Credential, $this>
     */
    public function credential(): BelongsTo
    {
        return $this->belongsTo(Credential::class);
    }

    #[Scope]
    protected function expiringWithin(Builder $query, int $days): Builder
    {
        return $query->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    #[Scope]
    protected function expired(Builder $query): Builder
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }
}
