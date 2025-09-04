<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\CredentialType;
use App\Traits\CanBeOrdered;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use Database\Factories\CredentialFactory;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Spatie\EloquentSortable\Sortable;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property CredentialType $type
 * @property int|null $issuer_id
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Issuer|null $issuer
 * @property-read string $label
 * @property-read string|null $relative_url
 * @property-read TrainingRecordCredential|null $pivot
 * @property-read Collection<int, TrainingRecord> $training_records
 * @property-read int|null $training_records_count
 * @property-read string|null $url
 *
 * @method static CredentialFactory factory($count = null, $state = [])
 * @method static Builder<static>|Credential newModelQuery()
 * @method static Builder<static>|Credential newQuery()
 * @method static Builder<static>|Credential ordered(string $direction = 'asc')
 * @method static Builder<static>|Credential query()
 * @method static Builder<static>|Credential whereCreatedAt($value)
 * @method static Builder<static>|Credential whereDescription($value)
 * @method static Builder<static>|Credential whereId($value)
 * @method static Builder<static>|Credential whereIssuerId($value)
 * @method static Builder<static>|Credential whereName($value)
 * @method static Builder<static>|Credential whereOrder($value)
 * @method static Builder<static>|Credential whereType($value)
 * @method static Builder<static>|Credential whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Credential extends Model implements HasLabel, Sortable
{
    use CanBeOrdered;
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;

    protected $fillable = [
        'name',
        'description',
        'type',
        'issuer_id',
    ];

    /**
     * @return BelongsTo<Issuer, $this>
     */
    public function issuer(): BelongsTo
    {
        return $this->belongsTo(Issuer::class);
    }

    /**
     * @return BelongsToMany<TrainingRecord, $this>
     */
    public function training_records(): BelongsToMany
    {
        return $this->belongsToMany(TrainingRecord::class, 'records_trainings_credentials')
            ->using(TrainingRecordCredential::class);
    }

    protected function casts(): array
    {
        return [
            'type' => CredentialType::class,
        ];
    }
}
