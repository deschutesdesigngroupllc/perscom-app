<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\CredentialType;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Credential extends Model
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasFactory;

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
