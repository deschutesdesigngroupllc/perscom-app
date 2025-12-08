<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $award_id
 * @property int $category_id
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Award $award
 * @property-read Category $category
 *
 * @method static Builder<static>|AwardCategory newModelQuery()
 * @method static Builder<static>|AwardCategory newQuery()
 * @method static Builder<static>|AwardCategory query()
 * @method static Builder<static>|AwardCategory whereAwardId($value)
 * @method static Builder<static>|AwardCategory whereCategoryId($value)
 * @method static Builder<static>|AwardCategory whereCreatedAt($value)
 * @method static Builder<static>|AwardCategory whereId($value)
 * @method static Builder<static>|AwardCategory whereOrder($value)
 * @method static Builder<static>|AwardCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class AwardCategory extends Pivot
{
    protected $table = 'awards_categories';

    protected $fillable = [
        'award_id',
        'category_id',
        'order',
        'created_at',
        'updated_at',
    ];

    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
