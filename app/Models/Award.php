<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\AwardScope;
use App\Traits\CanBeOrdered;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasAwardRecords;
use App\Traits\HasCategories;
use App\Traits\HasImages;
use App\Traits\HasLogs;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\EloquentSortable\Sortable;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Activity> $activities
 * @property-read int|null $activities_count
 * @property-read Collection<int, AwardRecord> $award_records
 * @property-read int|null $award_records_count
 * @property-read Collection<int, Award> $awards
 * @property-read int|null $awards_count
 * @property-read Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read Image|null $image
 * @property-read Collection<int, Image> $images
 * @property-read int|null $images_count
 * @property-read string $label
 * @property-read Collection<int, Activity> $logs
 * @property-read int|null $logs_count
 * @property-read string|null $relative_url
 * @property-read string|null $url
 *
 * @method static \Database\Factories\AwardFactory factory($count = null, $state = [])
 * @method static Builder<static>|Award newModelQuery()
 * @method static Builder<static>|Award newQuery()
 * @method static Builder<static>|Award ordered(string $direction = 'asc')
 * @method static Builder<static>|Award query()
 * @method static Builder<static>|Award whereCreatedAt($value)
 * @method static Builder<static>|Award whereDescription($value)
 * @method static Builder<static>|Award whereId($value)
 * @method static Builder<static>|Award whereName($value)
 * @method static Builder<static>|Award whereOrder($value)
 * @method static Builder<static>|Award whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[ScopedBy(AwardScope::class)]
class Award extends Model implements HasLabel, Sortable
{
    use CanBeOrdered;
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasAwardRecords;
    use HasCategories;
    use HasFactory;
    use HasImages;
    use HasLogs;
    use HasResourceLabel;
    use HasResourceUrl;

    protected $fillable = [
        'name',
        'description',
        'order',
        'created_at',
        'updated_at',
    ];
}
