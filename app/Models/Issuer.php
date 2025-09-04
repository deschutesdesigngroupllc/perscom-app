<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use Database\Factories\IssuerFactory;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $label
 * @property-read string|null $relative_url
 * @property-read string|null $url
 *
 * @method static IssuerFactory factory($count = null, $state = [])
 * @method static Builder<static>|Issuer newModelQuery()
 * @method static Builder<static>|Issuer newQuery()
 * @method static Builder<static>|Issuer query()
 * @method static Builder<static>|Issuer whereCreatedAt($value)
 * @method static Builder<static>|Issuer whereId($value)
 * @method static Builder<static>|Issuer whereName($value)
 * @method static Builder<static>|Issuer whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Issuer extends Model implements HasLabel
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;

    protected $fillable = [
        'name',
    ];
}
