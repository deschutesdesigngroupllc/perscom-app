<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $qualification_id
 * @property int $category_id
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Category $category
 * @property-read Qualification $qualification
 *
 * @method static Builder<static>|QualificationCategory newModelQuery()
 * @method static Builder<static>|QualificationCategory newQuery()
 * @method static Builder<static>|QualificationCategory query()
 * @method static Builder<static>|QualificationCategory whereCategoryId($value)
 * @method static Builder<static>|QualificationCategory whereCreatedAt($value)
 * @method static Builder<static>|QualificationCategory whereId($value)
 * @method static Builder<static>|QualificationCategory whereOrder($value)
 * @method static Builder<static>|QualificationCategory whereQualificationId($value)
 * @method static Builder<static>|QualificationCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class QualificationCategory extends Pivot
{
    protected $table = 'qualifications_categories';

    protected $fillable = [
        'qualification_id',
        'category_id',
        'order',
        'created_at',
        'updated_at',
    ];

    public function qualification(): BelongsTo
    {
        return $this->belongsTo(Qualification::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
