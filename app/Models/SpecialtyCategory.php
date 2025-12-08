<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $specialty_id
 * @property int $category_id
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Category $category
 * @property-read Specialty $specialty
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecialtyCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecialtyCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecialtyCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecialtyCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecialtyCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecialtyCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecialtyCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecialtyCategory whereSpecialtyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpecialtyCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
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
