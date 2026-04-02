<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasCategories;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read CompetencyCategory|null $categoryPivot
 * @property-read string $label
 * @property-read string|null $relative_url
 * @property-read TrainingRecordCompetency|null $pivot
 * @property-read Collection<int, TrainingRecord> $training_records
 * @property-read int|null $training_records_count
 * @property-read string|null $url
 *
 * @method static \Database\Factories\CompetencyFactory factory($count = null, $state = [])
 * @method static Builder<static>|Competency newModelQuery()
 * @method static Builder<static>|Competency newQuery()
 * @method static Builder<static>|Competency query()
 * @method static Builder<static>|Competency whereCreatedAt($value)
 * @method static Builder<static>|Competency whereDescription($value)
 * @method static Builder<static>|Competency whereId($value)
 * @method static Builder<static>|Competency whereName($value)
 * @method static Builder<static>|Competency whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Competency extends Model implements HasLabel
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasCategories;
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;

    protected $fillable = [
        'name',
        'description',
    ];

    public function training_records(): BelongsToMany
    {
        return $this->belongsToMany(TrainingRecord::class, 'records_trainings_competencies')
            ->using(TrainingRecordCompetency::class);
    }
}
