<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\CredentialType;
use App\Traits\CanBeOrdered;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EloquentSortable\Sortable;

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

    protected function casts(): array
    {
        return [
            'type' => CredentialType::class,
        ];
    }
}
