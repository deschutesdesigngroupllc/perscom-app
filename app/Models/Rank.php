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
use Illuminate\Database\Eloquent\SoftDeletes;
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
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 * @property-read string $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RankRecord> $rank_records
 * @property-read int|null $rank_records_count
 * @property-read string|null $relative_url
 * @property-read string|null $url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\RankFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Rank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rank onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Rank ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Rank query()
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank wherePaygrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Rank withoutTrashed()
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
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'abbreviation',
        'paygrade',
        'order',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
