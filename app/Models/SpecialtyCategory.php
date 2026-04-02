<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $specialty_id
 * @property int $category_id
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Category $category
 * @property-read Specialty $specialty
 *
 * @method static Builder<static>|SpecialtyCategory newModelQuery()
 * @method static Builder<static>|SpecialtyCategory newQuery()
 * @method static Builder<static>|SpecialtyCategory query()
 * @method static Builder<static>|SpecialtyCategory whereCategoryId($value)
 * @method static Builder<static>|SpecialtyCategory whereCreatedAt($value)
 * @method static Builder<static>|SpecialtyCategory whereId($value)
 * @method static Builder<static>|SpecialtyCategory whereOrder($value)
 * @method static Builder<static>|SpecialtyCategory whereSpecialtyId($value)
 * @method static Builder<static>|SpecialtyCategory whereUpdatedAt($value)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class SpecialtyCategory extends Pivot
{
    protected $table = 'specialties_categories';

    protected $fillable = [
        'specialty_id',
        'category_id',
        'order',
        'created_at',
        'updated_at',
    ];

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
