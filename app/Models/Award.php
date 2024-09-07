<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\AwardScope;
use App\Traits\CanBeOrdered;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAwardRecords;
use App\Traits\HasCategories;
use App\Traits\HasImages;
use App\Traits\HasLogs;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
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
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AwardRecord> $award_records
 * @property-read int|null $award_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 * @property-read string $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Support\Optional|string|null|null $relative_url
 * @property-read \Illuminate\Support\Optional|string|null|null $url
 *
 * @method static \Database\Factories\AwardFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Award newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Award newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Award onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Award ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Award query()
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Award whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Award withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Award withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ScopedBy(AwardScope::class)]
class Award extends Model implements HasLabel, Sortable
{
    use CanBeOrdered;
    use ClearsResponseCache;
    use HasAwardRecords;
    use HasCategories;
    use HasFactory;
    use HasImages;
    use HasLogs;
    use HasResourceLabel;
    use HasResourceUrl;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'order',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
