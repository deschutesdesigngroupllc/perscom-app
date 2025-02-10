<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\RankScope;
use App\Traits\CanBeOrdered;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasCategories;
use App\Traits\HasImages;
use App\Traits\HasRankRecords;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use App\Traits\HasUsers;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $abbreviation
 * @property string|null $paygrade
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Image> $images
 * @property-read int|null $images_count
 * @property-read string $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, RankRecord> $rank_records
 * @property-read int|null $rank_records_count
 * @property-read string|null $relative_url
 * @property-read string|null $url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\RankFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rank ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rank query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rank whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rank whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rank whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rank whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rank wherePaygrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rank whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[ScopedBy(RankScope::class)]
class Rank extends Model implements HasLabel, Sortable
{
    use CanBeOrdered;
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasCategories;
    use HasFactory;
    use HasImages;
    use HasRankRecords;
    use HasResourceLabel;
    use HasResourceUrl;
    use HasUsers;

    protected $fillable = [
        'name',
        'description',
        'abbreviation',
        'paygrade',
        'order',
        'created_at',
        'updated_at',
    ];
}
