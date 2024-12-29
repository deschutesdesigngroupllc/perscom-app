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
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 * @property-read string $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QualificationRecord> $qualification_records
 * @property-read int|null $qualification_records_count
 * @property-read string|null $relative_url
 * @property-read string|null $url
 *
 * @method static \Database\Factories\QualificationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Qualification whereUpdatedAt($value)
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

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'order',
        'created_at',
        'updated_at',
    ];
}
