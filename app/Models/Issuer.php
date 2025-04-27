<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $label
 * @property-read string|null $relative_url
 * @property-read string|null $url
 *
 * @method static \Database\Factories\IssuerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Issuer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Issuer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Issuer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Issuer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Issuer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Issuer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Issuer whereUpdatedAt($value)
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
