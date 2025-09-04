<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\QualificationScope;
use App\Traits\CanBeOrdered;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasCategories;
use App\Traits\HasImages;
use App\Traits\HasQualificationRecords;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use Database\Factories\QualificationFactory;
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
 * @property-read Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read Image|null $image
 * @property-read Collection<int, Image> $images
 * @property-read int|null $images_count
 * @property-read string $label
 * @property-read Collection<int, QualificationRecord> $qualification_records
 * @property-read int|null $qualification_records_count
 * @property-read Collection<int, Qualification> $qualifications
 * @property-read int|null $qualifications_count
 * @property-read string|null $relative_url
 * @property-read string|null $url
 *
 * @method static QualificationFactory factory($count = null, $state = [])
 * @method static Builder<static>|Qualification newModelQuery()
 * @method static Builder<static>|Qualification newQuery()
 * @method static Builder<static>|Qualification ordered(string $direction = 'asc')
 * @method static Builder<static>|Qualification query()
 * @method static Builder<static>|Qualification whereCreatedAt($value)
 * @method static Builder<static>|Qualification whereDescription($value)
 * @method static Builder<static>|Qualification whereId($value)
 * @method static Builder<static>|Qualification whereName($value)
 * @method static Builder<static>|Qualification whereOrder($value)
 * @method static Builder<static>|Qualification whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[ScopedBy(QualificationScope::class)]
class Qualification extends Model implements HasLabel, Sortable
{
    use CanBeOrdered;
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasCategories;
    use HasFactory;
    use HasImages;
    use HasQualificationRecords;
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
