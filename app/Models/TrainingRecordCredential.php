<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\TrainingRecordCredentialObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @method static Builder<static>|TrainingRecordCredential newModelQuery()
 * @method static Builder<static>|TrainingRecordCredential newQuery()
 * @method static Builder<static>|TrainingRecordCredential query()
 *
 * @mixin \Eloquent
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

    protected function scopeExpiringWithin(Builder $query, int $days): Builder
    {
        return $query->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    protected function scopeExpired(Builder $query): Builder
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
